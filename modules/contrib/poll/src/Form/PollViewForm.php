<?php

namespace Drupal\poll\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Render\RendererInterface;
use Drupal\poll\PollInterface;
use Drupal\poll\PollVoteStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays banned IP addresses.
 */
class PollViewForm extends FormBase implements BaseFormIdInterface {

  /**
   * The Poll of the form.
   *
   * @var \Drupal\poll\PollInterface
   */
  protected $poll;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * The page cache disabling policy.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected KillSwitch $pageCacheKillSwitch;

  /**
   * The poll vote storage service.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected PollVoteStorageInterface $pollVoteStorage;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $timeInterface;

  /**
   * {@inheritdoc}
   */
  public function getBaseFormId() {
    return 'poll_view_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'poll_view_form_' . $this->poll->id();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->renderer = $container->get('renderer');
    $instance->pageCacheKillSwitch = $container->get('page_cache_kill_switch');
    $instance->pollVoteStorage = $container->get('poll_vote.storage');
    $instance->timeInterface = $container->get('datetime.time');
    return $instance;
  }

  /**
   * Set the Poll of this form.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll that will be set in the form.
   */
  public function setPoll(PollInterface $poll) {
    $this->poll = $poll;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?Request $request = NULL, $view_mode = 'full') {
    // Add the poll to the form.
    $form['poll']['#type'] = 'value';
    $form['poll']['#value'] = $this->poll;
    $form['#view_mode'] = $view_mode;

    if ($this->showResults($this->poll, $form_state)) {
      // Check if the user already voted. The form is still being built but
      // the Vote button won't be added so the submit callbacks will not be
      // called. Directly check for the request method and use the raw user
      // input.
      if ($request->isMethod('POST') && $this->poll->hasUserVoted() && !$this->poll->isVotingAllowed($this->poll)) {
        $input = $form_state->getUserInput();
        if (isset($input['op']) && $input['op'] == $this->t('Vote')) {
          // If this happened, then the form submission was likely a cached
          // page. Force a session for this user so the results become visible.
          $this->messenger()->addError($this->t('Your vote for this poll has already been submitted.'));
          $_SESSION['poll_vote'][$this->poll->id()] = FALSE;
        }
      }

      $form['results'] = $this->showPollResults($this->poll, $view_mode);

      // For all view modes except full and block (as block displays it as the
      // block title), display the question.
      if ($view_mode != 'full' && $view_mode != 'block') {
        $form['results']['#show_question'] = TRUE;
      }
    }
    else {
      $options = $this->poll->getOptions();
      if ($options) {
        $form['choice'] = [
          '#type' => 'radios',
          '#title' => $this->t('Choices'),
          '#title_display' => 'invisible',
          '#options' => $options,
        ];
        if ($this->poll->getAutoSubmit()) {
          $form['choice']['#attributes']['class'][] = 'poll-auto-submit';
          $form['#attached'] = ['library' => ['poll/auto-submit']];
        }
      }
      $form['#theme'] = 'poll_vote';
      $form['#entity'] = $this->poll;
      $form['#action'] = $this->poll->toUrl()->setOption('query', $this->getDestinationArray())->toString();
      // Set a flag to hide results which will be removed if we want to view
      // results when the form is rebuilt.
      $form_state->set('show_results', FALSE);

      // For all view modes except full and block (as block displays it as the
      // block title), display the question.
      if ($view_mode != 'full' && $view_mode != 'block') {
        $form['#show_question'] = TRUE;
      }

    }

    $form['actions'] = $this->actions($form, $form_state, $this->poll);

    $form['#cache'] = [
      'tags' => $this->poll->getCacheTags(),
    ];

    return $form;
  }

  /**
   * Form submit handler to replace the poll form.
   */
  public function ajaxReplaceForm(array $form, FormStateInterface $form_state) {
    // Embed status message into the form.
    $form = ['messages' => ['#type' => 'status_messages']] + $form;
    // Render the form.
    $output = $this->renderer->renderRoot($form);

    $response = new AjaxResponse();
    $response->setAttachments($form['#attached']);

    // Replace the form completely and return it.
    return $response->addCommand(new ReplaceCommand('.poll-view-form-' . $this->poll->id(), $output));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Checks if the voting results should be shown.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll entity.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The complete state of the form.
   *
   * @return bool
   *   True if the voting results may be shown. False otherwise.
   */
  public function showResults(PollInterface $poll, FormStateInterface $form_state) {
    // The "View results" button, when available, has been clicked.
    if ($form_state->get('show_results')) {
      return TRUE;
    }

    // A vote has been recorded and the "View poll" button hasn't been
    // clicked. This check is required when an anonymous user is allowed to
    // vote more than once.
    if ($this->currentUser()->isAnonymous() && !empty($_SESSION['poll_vote'][$poll->id()]) && $form_state->get('show_results') === NULL) {
      return TRUE;
    }

    // If we have voted, but have no restrictions, we still go to the results
    // after each vote.
    $input = $form_state->getUserInput();
    if ($this->currentUser()->isAnonymous() && $poll->getVoteRestriction() === PollInterface::ANONYMOUS_VOTE_RESTRICT_NONE && (isset($input['_triggering_element_value']) && $input['_triggering_element_value'] == $this->t('Vote'))) {
      return TRUE;
    }

    // Voting is no longer allowed.
    if (!$poll->isVotingAllowed($poll)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Returns the form actions.
   *
   * @return array
   *   A renderable array of form actions.
   */
  protected function actions(array $form, FormStateInterface $form_state, PollInterface $poll) {
    $actions = [];

    // Default ajax behavior, use the poll URL for faster submission, this
    // requires that we explicitly provide the ajax_form query argument too in
    // the separate options key, as that replaces all options of the Url object.
    $ajax = [
      'callback' => '::ajaxReplaceForm',
      'url' => $this->poll->toUrl(),
      'options' => [
        'query' => [
          FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          'view_mode' => $form['#view_mode'],
        ],
      ],
    ];

    if ($this->showResults($poll, $form_state)) {
      // Allow user to cancel their vote.
      if ($poll->isCancelAllowed($poll)) {
        $actions['#type'] = 'actions';
        $actions['cancel']['#type'] = 'submit';
        $actions['cancel']['#button_type'] = 'primary';
        $actions['cancel']['#value'] = $this->t('Cancel vote');
        $actions['cancel']['#submit'] = ['::cancel'];
        $actions['cancel']['#ajax'] = $ajax;
        $actions['cancel']['#weight'] = '0';
        $actions['cancel']['#id'] = 'edit-cancel--' . $this->poll->id();
      }
      // Allow to go back from the results screen to the poll if you're still
      // allowed to cast a vote.
      if ($poll->isVotingAllowed($poll)) {
        $actions['#type'] = 'actions';
        $actions['back']['#type'] = 'submit';
        $actions['back']['#button_type'] = 'primary';
        $actions['back']['#value'] = $this->t('View poll');
        $actions['back']['#submit'] = ['::back'];
        $actions['back']['#ajax'] = $ajax;
        $actions['back']['#weight'] = '0';
        $actions['back']['#id'] = 'edit-back--' . $this->poll->id();
      }
    }
    else {
      $actions['#type'] = 'actions';
      $actions['vote']['#type'] = 'submit';
      $actions['vote']['#button_type'] = 'primary';
      $actions['vote']['#value'] = $this->t('Vote');
      $actions['vote']['#validate'] = ['::validateVote'];
      $actions['vote']['#submit'] = ['::save'];
      $actions['vote']['#ajax'] = $ajax;
      $actions['vote']['#weight'] = '0';
      $actions['vote']['#id'] = 'edit-vote--' . $this->poll->id();
      if ($poll->getAutoSubmit()) {
        $actions['vote']['#attributes']['class'][] = 'visually-hidden';
      }

      // View results before voting.
      if ($poll->result_vote_allow->value || $this->currentUser()->hasPermission('view poll results')) {
        $actions['result']['#type'] = 'submit';
        $actions['result']['#button_type'] = 'primary';
        $actions['result']['#value'] = $this->t('View results');
        $actions['result']['#submit'] = ['::result'];
        $actions['result']['#ajax'] = $ajax;
        $actions['result']['#weight'] = '1';
        $actions['result']['#id'] = 'edit-result--' . $this->poll->id();
      }
    }

    return $actions;
  }

  /**
   * Display a themed poll results.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll entity.
   * @param string $view_mode
   *   The poll view mode.
   * @param bool $block
   *   (optional) TRUE if a poll should be displayed in a block. Defaults to
   *   FALSE.
   *
   * @return array
   *   A renderable array.
   */
  public function showPollResults(PollInterface $poll, $view_mode = 'default', $block = FALSE) {
    // Ensure that a page that shows poll results can not be cached.
    $this->pageCacheKillSwitch->trigger();

    $total_votes = 0;
    foreach ($poll->getVotes() as $vote) {
      $total_votes += $vote;
    }

    $options = $poll->getOptions();
    $current_user_vote = $this->pollVoteStorage->getUserVote($poll);
    $poll_results = [];
    $votes = $poll->getVotes();
    switch ($poll->getVotesOrderType()) {
      case PollInterface::VOTES_ORDER_COUNT_ASC:
        asort($votes);
        break;

      case PollInterface::VOTES_ORDER_COUNT_DESC:
        arsort($votes);
        break;
    }
    foreach ($votes as $pid => $vote) {
      $percentage = round($vote * 100 / max($total_votes, 1));
      $display_votes = (!$block) ? ' (' . $this->getStringTranslation()
        ->formatPlural($vote, '1 vote', '@count votes') . ')' : '';
      $current_vote = $current_user_vote ? $current_user_vote['chid'] : FALSE;
      $poll_results[] = [
        '#theme' => 'poll_meter',
        '#choice' => ['#markup' => $options[$pid]],
        '#is_current_selection' => (int) $current_vote === $pid,
        '#display_value' => $this->t('@percentage%', ['@percentage' => $percentage]) . $display_votes,
        '#min' => 0,
        '#max' => $total_votes,
        '#value' => $vote,
        '#percentage' => $percentage,
        '#attributes' => ['class' => ['bar']],
        '#poll' => $poll,
      ];
    }

    $user_vote = $this->pollVoteStorage->getUserVote($poll);

    $output = [
      '#theme' => 'poll_results',
      '#raw_question' => $poll->label(),
      '#results' => $poll_results,
      '#votes' => $total_votes,
      '#block' => $block,
      '#pid' => $poll->id(),
      '#poll' => $poll,
      '#view_mode' => $view_mode,
      '#vote' => $user_vote['chid'] ?? NULL,
    ];

    return $output;
  }

  /**
   * Cancel vote submit function.
   *
   * @param array $form
   *   The previous form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function cancel(array $form, FormStateInterface $form_state) {
    $this->pollVoteStorage->cancelVote($this->poll, $this->currentUser());
    $this->logger('poll')->notice('%user\'s vote in Poll #%poll deleted.', [
      '%user' => $this->currentUser()->id(),
      '%poll' => $this->poll->id(),
    ]);
    $this->messenger()->addMessage($this->t('Your vote was cancelled.'));

    // In case of an ajax submission, trigger a form rebuild so that we can
    // return an updated form through the ajax callback.
    if ($this->getRequest()->query->get('ajax_form')) {
      $form_state->setRebuild(TRUE);
    }
  }

  /**
   * Form submit handler to display the poll results.
   */
  public function result(array $form, FormStateInterface $form_state) {
    $form_state->set('show_results', TRUE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Form submit handler to return back to poll view submit function.
   */
  public function back(array $form, FormStateInterface $form_state) {
    $form_state->set('show_results', FALSE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Form submit handler to save a user's vote.
   */
  public function save(array $form, FormStateInterface $form_state) {
    $options = [];
    $options['chid'] = $form_state->getValue('choice');
    $options['uid'] = $this->currentUser()->id();
    $options['pid'] = $form_state->getValue('poll')->id();
    $options['hostname'] = $this->getRequest()->getClientIp();
    $options['timestamp'] = $this->timeInterface->getRequestTime();
    // Save vote.
    $vote_id = $this->pollVoteStorage->saveVote($options);
    $this->messenger()->addMessage($this->t('Your vote has been recorded.'));

    if ($this->currentUser()->isAnonymous()) {
      $poll_id = $form_state->getValue('poll')->id();
      // The vote is recorded so the user gets the result view instead of the
      // voting form when viewing the poll. Saving a value in $_SESSION has the
      // convenient side effect of preventing the user from hitting the page
      // cache. When anonymous voting is allowed, the page cache should only
      // contain the voting form, not the results.
      $_SESSION['poll_vote'][$poll_id] = $vote_id;
    }

    // In case of an ajax submission, trigger a form rebuild so that we can
    // return an updated form through the ajax callback.
    if ($this->getRequest()->query->get('ajax_form')) {
      $form_state->setRebuild(TRUE);
    }

    // No explicit redirect, so that we stay on the current page, which might
    // be the poll form or another page that is displaying this poll, for
    // example as a block.
  }

  /**
   * Validation handler for the vote action.
   */
  public function validateVote(array &$form, FormStateInterface $form_state) {
    if (!$form_state->hasValue('choice')) {
      $form_state->setErrorByName('choice', $this->t('Make a selection before voting.'));
    }
  }

}
