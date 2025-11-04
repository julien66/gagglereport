<?php

namespace Drupal\rules\Form;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Core\RulesConfigurableEventHandlerInterface;
use Drupal\rules\Core\RulesEventManager;
use Drupal\rules\Ui\RulesUiHandlerInterface;
use Drupal\rules\Entity\ReactionRuleConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UI form to add an event to a rule.
 */
class AddEventForm extends FormBase {
  use AddEventFormTrait;

  /**
   * The Reaction Rule being modified.
   *
   * @var \Drupal\rules\Entity\ReactionRuleConfig
   */
  protected $reactionRule;

  /**
   * Constructs a new event add form.
   *
   * @param \Drupal\rules\Core\RulesEventManager $event_manager
   *   The Rules event manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_bundle_info
   *   The entity type bundle information manager.
   */
  public function __construct(RulesEventManager $event_manager, EntityTypeBundleInfoInterface $entity_bundle_info) {
    $this->eventManager = $event_manager;
    $this->entityBundleInfo = $entity_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.rules_event'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_add_event';
  }

  /**
   * Provides the page title on the form.
   */
  public function getTitle(RulesUiHandlerInterface $rules_ui_handler) {
    return $this->t('Add event to %rule', ['%rule' => $rules_ui_handler->getComponentLabel()]);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ReactionRuleConfig $rules_reaction_rule = NULL) {
    $this->reactionRule = $rules_reaction_rule;
    $form = $this->buildEventForm($form, $form_state);

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event_name = $form_state->getValue(['events', 0, 'event_name']);

    // Check if the selected event is an entity event.
    $event_definition = $this->eventManager->getDefinition($event_name);
    $handler_class = $event_definition['class'];
    if (is_subclass_of($handler_class, RulesConfigurableEventHandlerInterface::class)) {
      // Support non-javascript browsers.
      if (!array_key_exists('bundle', $form_state->getValues())) {
        // The form field for "bundle" was not displayed yet, so rebuild the
        // form so that the user gets a chance to fill it in.
        $form_state->setRebuild();
        return;
      }

      // Add the bundle name to the event name if a bundle was selected.
      $this->entityBundleBuilder('rules_reaction_rule', $this->reactionRule, $form, $form_state);
      $event_name = $form_state->getValue(['events', 0, 'event_name']);
    }

    $this->reactionRule->addEvent($event_name);
    $this->reactionRule->save();

    $this->messenger()->addMessage($this->t('Added event %label to %rule.', [
      '%label' => $this->eventManager->getDefinition($event_name)['label'],
      '%rule' => $this->reactionRule->label(),
    ]));
    $form_state->setRedirect('entity.rules_reaction_rule.edit_form', [
      'rules_reaction_rule' => $this->reactionRule->id(),
    ]);
  }

}
