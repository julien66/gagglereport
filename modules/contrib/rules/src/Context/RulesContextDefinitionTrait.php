<?php

namespace Drupal\rules\Context;

/**
 * Implements the methods of \Drupal\rules\Context\ContextDefinitionInterface.
 *
 * This class overrides the core ContextDefinition to provide Rules-specific
 * functionality, but also to preserve the core Drupal behavior of
 * ContextDefinition without triggering deprecated code. Specifically, in Rules
 * we need to be able to create a ContextDefinition for ANY typed data object,
 * without prior knowledge of what that type is; we need to be able to say
 * new ContextDefinition($type) or ContextDefinition::create($type) where $type
 * may be 'integer', or 'string', or 'entity:node' etc.
 *
 * This worked in Drupal 8, but in Drupal 9 we now have to use different classes
 * for different types. Thus the core ContextDefinition::create($type) will work
 * ONLY for non-entity types, and we have to use a different way to create
 * context definitions for entities. This is a problem because now there is no
 * factory method to create context definitions, and we have to test each type
 * and just "know" the correct class to use for that type to create a context
 * definition.
 *
 * This Drupal 9 behavior is unworkable in a module like Rules where we rely on
 * introspection and reflection to manipulate typed data. Without some way to
 * programmatically create a context definition for an arbitrary data type,
 * Rules will not work.
 *
 * To work around this, we override the core ContextDefinition's __construct()
 * and create() methods. In the parent::__construct(), there is an explicit
 * assert that prevents ContextDefinition from being used for an entity. We
 * remove that here - the __construct() method of this class is otherwise
 * identical to the parent. We also override the core ContextDefinition's
 * create() method to create a Rules version of EntityContextDefinition when
 * ContextDefinition is created for an entity type. This is necessary because
 * the core EntityContextDefinition doesn't have the necessary Rules extensions
 * and there is no multiple inheritance in PHP so we have to extend
 * ContextDefinition and EntityContextDefinition separately.
 *
 * This is a poor solution that will work for existing core Rules use-cases, as
 * EntityContextDefinition is never used directly in Rules, but this may not
 * work for modules that extend Rules. A proper and permanent solution will
 * require a change to core Drupal.
 *
 * @see \Drupal\rules\Context\EntityContextDefinition
 * @see https://www.drupal.org/project/rules/issues/3161582
 * @see https://www.drupal.org/project/drupal/issues/3126747
 */
trait RulesContextDefinitionTrait {

  /**
   * The mapping of config export keys to internal properties.
   *
   * @var array
   */
  public static $nameMap = [
    'type' => 'dataType',
    'label' => 'label',
    'description' => 'description',
    'multiple' => 'isMultiple',
    'required' => 'isRequired',
    'default_value' => 'defaultValue',
    'constraints' => 'constraints',
    'getter' => 'getter',
    'allow_null' => 'allowNull',
    'assignment_restriction' => 'assignmentRestriction',
    'options_provider' => 'optionsProvider',
  ];

  /**
   * Name of getter function for this context variable.
   *
   * Only applicable for events.
   *
   * @var string
   */
  public ?string $getter = NULL;

  /**
   * Whether the context value is allowed to be NULL or not.
   *
   * @var bool
   */
  public bool $allowNull = FALSE;

  /**
   * The assignment restriction of this context.
   *
   * @var string|null
   *
   * @see \Drupal\rules\Context\ContextDefinitionInterface::getAssignmentRestriction()
   */
  public ?string $assignmentRestriction = NULL;

  /**
   * The options provider for this context.
   *
   * @var class-string|null
   */
  public ?string $optionsProvider = NULL;

  /**
   * {@inheritdoc}
   */
  public function hasGetter(): bool {
    return !is_null($this->getter);
  }

  /**
   * {@inheritdoc}
   */
  public function getGetter(): ?string {
    return $this->getter;
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedNull(): bool {
    return $this->allowNull;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowNull(bool $null_allowed): static {
    $this->allowNull = $null_allowed;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssignmentRestriction(): ?string {
    return $this->assignmentRestriction;
  }

  /**
   * {@inheritdoc}
   */
  public function setAssignmentRestriction(?string $restriction): static {
    $this->assignmentRestriction = $restriction;
    return $this;
  }

}
