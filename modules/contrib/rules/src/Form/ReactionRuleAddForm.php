<?php

namespace Drupal\rules\Form;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Core\RulesEventManager;
use Drupal\rules\Engine\ExpressionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to add a reaction rule.
 */
class ReactionRuleAddForm extends RulesComponentFormBase {
  use AddEventFormTrait;

  /**
   * Constructs a new reaction rule form.
   *
   * @param \Drupal\rules\Engine\ExpressionManagerInterface $expression_manager
   *   The expression manager.
   * @param \Drupal\rules\Core\RulesEventManager $event_manager
   *   The Rules event manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_bundle_info
   *   The entity type bundle information manager.
   */
  public function __construct(ExpressionManagerInterface $expression_manager, RulesEventManager $event_manager, EntityTypeBundleInfoInterface $entity_bundle_info) {
    parent::__construct($expression_manager);
    $this->eventManager = $event_manager;
    $this->entityBundleInfo = $entity_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.rules_expression'),
      $container->get('plugin.manager.rules_event'),
      $container->get('entity_type.bundle.info')
    );
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
  public function form(array $form, FormStateInterface $form_state) {
    return $this->buildEventForm($form, $form_state) + parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    $this->messenger()->addMessage($this->t('Reaction rule %label has been created.', [
      '%label' => $this->entity->label(),
    ]));
    $form_state->setRedirect('entity.rules_reaction_rule.edit_form', [
      'rules_reaction_rule' => $this->entity->id(),
    ]);
  }

}
