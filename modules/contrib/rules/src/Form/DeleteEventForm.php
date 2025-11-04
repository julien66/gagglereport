<?php

namespace Drupal\rules\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Core\RulesEventManager;
use Drupal\rules\Entity\ReactionRuleConfig;
use Drupal\rules\Ui\RulesUiHandlerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removes an event from a rule.
 */
class DeleteEventForm extends ConfirmFormBase {

  /**
   * The Rules event manager.
   *
   * @var \Drupal\rules\Core\RulesEventManager
   */
  protected $eventManager;

  /**
   * The RulesUI handler of the currently active UI.
   *
   * @var \Drupal\rules\Ui\RulesUiHandlerInterface
   */
  protected $rulesUiHandler;

  /**
   * The Reaction Rule being modified.
   *
   * @var \Drupal\rules\Entity\ReactionRuleConfig
   */
  protected $reactionRule;

  /**
   * The ID of the event in the rule.
   *
   * @var string
   */
  protected $id;

  /**
   * Constructs a new event delete form.
   *
   * @param \Drupal\rules\Core\RulesEventManager $event_manager
   *   The Rules event plugin manager.
   */
  public function __construct(RulesEventManager $event_manager) {
    $this->eventManager = $event_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.rules_event')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_delete_event';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\rules\Ui\RulesUiHandlerInterface $rules_ui_handler
   *   The RulesUI handler of the currently active UI.
   * @param \Drupal\rules\Entity\ReactionRuleConfig $rules_reaction_rule
   *   The rule config object this form is for.
   * @param string $id
   *   The ID of the event in the rule.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, RulesUiHandlerInterface $rules_ui_handler = NULL, ReactionRuleConfig $rules_reaction_rule = NULL, $id = NULL) {
    $this->rulesUiHandler = $rules_ui_handler;
    $this->reactionRule = $rules_reaction_rule;
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    // Do not allow to delete an event if there's only one.
    if (count($this->reactionRule->getEvents()) === 1) {
      throw new AccessDeniedHttpException('An event cannot be deleted if the reaction rule has only one.');
    }

    // Check of the event requested to be deleted, exists.
    if (!$this->reactionRule->hasEvent($this->id)) {
      throw new NotFoundHttpException();
    }

    $event_definition = $this->eventManager->getDefinition($this->id);

    return $this->t('Are you sure you want to delete the event %title from %rule?', [
      '%title' => $event_definition['label'],
      '%rule' => $this->rulesUiHandler->getComponentLabel(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->rulesUiHandler->getBaseRouteUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->reactionRule->removeEvent($this->id);
    $this->reactionRule->save();

    $this->messenger()->addMessage($this->t('Deleted event %label from %rule.', [
      '%label' => $this->eventManager->getDefinition($this->id)['label'],
      '%rule' => $this->reactionRule->label(),
    ]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
