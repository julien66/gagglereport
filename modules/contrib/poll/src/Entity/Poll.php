<?php

namespace Drupal\poll\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\poll\PollInterface;
use Drupal\user\UserInterface;

/**
 * Defines the poll entity class.
 *
 * @ContentEntityType(
 *   id = "poll",
 *   label = @Translation("Poll"),
 *   handlers = {
 *     "access" = "\Drupal\poll\PollAccessControlHandler",
 *     "storage" = "Drupal\poll\PollStorage",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "list_builder" = "Drupal\poll\PollListBuilder",
 *     "view_builder" = "Drupal\poll\PollViewBuilder",
 *     "views_data" = "Drupal\poll\PollViewsData",
 *     "form" = {
 *       "default" = "Drupal\poll\Form\PollForm",
 *       "edit" = "Drupal\poll\Form\PollForm",
 *       "delete" = "Drupal\poll\Form\PollDeleteForm",
 *       "delete_vote" = "Drupal\poll\Form\PollVoteDeleteForm",
 *       "delete_items" = "Drupal\poll\Form\PollItemsDeleteForm",
 *     }
 *   },
 *   links = {
 *     "canonical" = "/poll/{poll}",
 *     "edit-form" = "/poll/{poll}/edit",
 *     "delete-form" = "/poll/{poll}/delete"
 *   },
 *   base_table = "poll",
 *   data_table = "poll_field_data",
 *   admin_permission = "administer polls",
 *   field_ui_base_route = "poll.settings",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "question",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status"
 *   }
 * )
 */
class Poll extends ContentEntityBase implements EntityPublishedInterface, PollInterface {

  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function setQuestion($question) {
    $this->set('question', $question);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreated($created) {
    $this->set('created', $created);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreated() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntime() {
    return (int) $this->get('runtime')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRuntime(int $runtime) {
    $this->set('runtime', $runtime);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAnonymousVoteAllow() {
    return $this->get('anonymous_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAnonymousVoteAllow($anonymous_vote_allow) {
    $this->set('anonymous_vote_allow', $anonymous_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelVoteAllow() {
    return $this->get('cancel_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCancelVoteAllow($cancel_vote_allow) {
    $this->set('cancel_vote_allow', $cancel_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultVoteAllow() {
    return $this->get('result_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setResultVoteAllow($result_vote_allow) {
    $this->set('result_vote_allow', $result_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isOpen() {
    return (bool) $this->get('active')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isClosed() {
    return (bool) $this->get('active')->value == 0;
  }

  /**
   * {@inheritdoc}
   */
  public function close() {
    return $this->set('active', 0);
  }

  /**
   * {@inheritdoc}
   */
  public function open() {
    return $this->set('active', 1);
  }

  /**
   * {@inheritdoc}
   */
  public function getAutoSubmit() {
    return (bool) $this->get('auto_submit')->value == 1;
  }

  /**
   * {@inheritdoc}
   */
  public function setAutoSubmit($submit) {
    $this->set('auto_submit', $submit);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVoteRestriction() {
    return $this->get('anonymous_vote_restriction')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Poll ID'))
      ->setDescription(t('The ID of the poll.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The poll author.'))
      ->setSetting('target_type', 'user')
      ->setTranslatable(TRUE)
      ->setDefaultValueCallback('Drupal\poll\Entity\Poll::getCurrentUserId')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -80,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The poll UUID.'))
      ->setReadOnly(TRUE);

    $fields['question'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Question'))
      ->setDescription(t('The poll question.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -100,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The poll language code.'));

    $fields['choice'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Choice'))
      ->setSetting('target_type', 'poll_choice')
      ->setDescription(t('Enter the poll choices.'))
      ->setRequired(TRUE)
      // The number and order of choices may not be translated, only the
      // referenced choices.
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'poll_choice_default',
        'settings' => [],
        'weight' => -90,
      ])
      ->setDisplayConfigurable('form', TRUE);

    // Poll attributes.
    $duration = [
      // 1-6 days.
      86400,
      2 * 86400,
      3 * 86400,
      4 * 86400,
      5 * 86400,
      6 * 86400,
      // 1-3 weeks (7 days).
      604800,
      2 * 604800,
      3 * 604800,
      // 1-3,6,9 months (30 days).
      2592000,
      2 * 2592000,
      3 * 2592000,
      6 * 2592000,
      9 * 2592000,
      // 1 year (365 days).
      31536000,
    ];

    $period = [0 => t('Unlimited')] + array_map([
      \Drupal::service('date.formatter'),
      'formatInterval',
    ], array_combine($duration, $duration));

    $fields['runtime'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Poll Duration'))
      ->setDescription(t('After this period, the poll will be closed automatically.'))
      ->setSetting('unsigned', TRUE)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', $period)
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -50,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['auto_submit'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Auto submit voting form'))
      ->setDescription(t('If enabled the voting form will submit as soon as a choice is selected.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['order_results'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Order results'))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
          PollInterface::VOTES_ORDER_WEIGHT => 'By weight (default)',
          PollInterface::VOTES_ORDER_COUNT_ASC => 'By votes count (ascending)',
          PollInterface::VOTES_ORDER_COUNT_DESC => 'By votes count (descending)',
        ],
      ])
      ->setDefaultValue(PollInterface::VOTES_ORDER_WEIGHT)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -30,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['anonymous_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow anonymous votes'))
      ->setDescription(t('A flag indicating whether anonymous users are allowed to vote.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['anonymous_vote_restriction'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Anonymous vote restriction'))
      ->setSetting('allowed_values', [
        PollInterface::ANONYMOUS_VOTE_RESTRICT_IP => t('One vote per IP'),
        PollInterface::ANONYMOUS_VOTE_RESTRICT_SESSION => t('One vote per session'),
        PollInterface::ANONYMOUS_VOTE_RESTRICT_NONE => t('Unlimited votes'),
      ])
      ->setDefaultValue(PollInterface::ANONYMOUS_VOTE_RESTRICT_IP)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['cancel_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow cancel votes'))
      ->setDescription(t('A flag indicating whether users may cancel their vote.'))
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['result_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow view results'))
      ->setDescription(t('A flag indicating whether users may see the results before voting.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDescription(t('A flag indicating whether the poll is published.'))
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -70,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['active'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDescription(t('A flag indicating whether the poll is active.'))
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -60,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('When the poll was created, as a Unix timestamp.'));

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public static function sort($a, $b) {
    return strcmp($a->label(), $b->label());
  }

  /**
   * {@inheritdoc}
   */
  public function hasUserVoted() {
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    return $vote_storage->getUserVote($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];
    if (count($this->choice)) {
      foreach ($this->choice as $choice_item) {
        $options[$choice_item->target_id] = \Drupal::service('entity.repository')->getTranslationFromContext($choice_item->entity, $this->language()->getId())->label();
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionValues() {
    $options = [];
    if (count($this->choice)) {
      foreach ($this->choice as $choice_item) {
        $options[$choice_item->target_id] = 1;
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    foreach ($this->choice as $choice_item) {
      if ($choice_item->entity && $choice_item->entity->needsSaving()) {
        $choice_item->entity->save();
        $choice_item->target_id = $choice_item->entity->id();
      }
    }

    // Delete no longer referenced choices.
    if (!$this->isNew()) {
      $original_choices = [];
      foreach ($this->original->choice as $choice_item) {
        $original_choices[] = $choice_item->target_id;
      }

      $current_choices = [];
      foreach ($this->choice as $choice_item) {
        $current_choices[] = $choice_item->target_id;
      }

      $removed_choices = array_filter(array_diff($original_choices, $current_choices));
      if ($removed_choices) {
        \Drupal::service('poll_vote.storage')->deleteChoicesVotes($removed_choices);
        $storage = \Drupal::entityTypeManager()->getStorage('poll_choice');
        $storage->delete($storage->loadMultiple($removed_choices));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');

    // Delete votes.
    foreach ($entities as $entity) {
      $vote_storage->deleteVotes($entity);
    }

    // Delete referenced choices.
    $choices = [];
    foreach ($entities as $entity) {
      $choices = array_merge($choices, $entity->choice->referencedEntities());
    }
    if ($choices) {
      \Drupal::entityTypeManager()->getStorage('poll_choice')->delete($choices);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getVotes() {
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    return $vote_storage->getVotes($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getVotesOrderType() {
    return $this->get('order_results')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVotesOrderType($order_type) {
    $this->set('order_results', $order_type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isCancelAllowed(PollInterface $poll): bool {
    $current_user = \Drupal::currentUser();
    // Allow access if the user has voted.
    return $poll->hasUserVoted()
      // And the poll allows to cancel votes.
      && $poll->getCancelVoteAllow()
      // And the user has the cancel own vote permission.
      && $current_user->hasPermission('cancel own vote')
      // And the user is authenticated or the session contains the voted flag.
      && ($current_user->isAuthenticated() || !empty($_SESSION['poll_vote'][$poll->id()]))
      // And poll is open.
      && $poll->isOpen();
  }

  /**
   * {@inheritdoc}
   */
  public function isVotingAllowed(PollInterface $poll): bool {
    $current_user = \Drupal::currentUser();
    // The current user must have access to vote.
    if (!$current_user->hasPermission('access polls')) {
      return FALSE;
    }

    // If the poll is closed, we're not allowed to vote.
    if ($poll->isClosed()) {
      return FALSE;
    }

    // Vote restrictions for anonymous users.
    if ($current_user->isAnonymous()) {
      // If the poll doesn't allow anonymous users to vote, we return.
      if (!$poll->getAnonymousVoteAllow()) {
        return FALSE;
      }

      // We're allowed to vote as anonymous but check the restrictions.
      switch ($poll->getVoteRestriction()) {
        // No restrictions, so we can vote again.
        case PollInterface::ANONYMOUS_VOTE_RESTRICT_NONE:
          return TRUE;

        case PollInterface::ANONYMOUS_VOTE_RESTRICT_IP:
        case PollInterface::ANONYMOUS_VOTE_RESTRICT_SESSION:
          if (!$poll->hasUserVoted()) {
            return TRUE;
          }
      }
    }

    // If the user hasn't voted yet, voting is allowed.
    if (!$poll->hasUserVoted()) {
      return TRUE;
    }

    return FALSE;
  }

}
