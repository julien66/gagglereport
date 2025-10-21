<?php

namespace Drupal\poll;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\poll\Form\PollViewForm;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a service for poll post render cache callbacks.
 */
class PollPostRenderCache implements TrustedCallbackInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected ClassResolverInterface $classResolver;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected RequestStack $requestStack;

  /**
   * Constructs a new PollPostRenderCache object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $class_resolver
   *   The class resolver service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The current request.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ClassResolverInterface $class_resolver, FormBuilderInterface $form_builder, RequestStack $request_stack) {
    $this->entityTypeManager = $entity_type_manager;
    $this->classResolver = $class_resolver;
    $this->formBuilder = $form_builder;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks() {
    return ['renderViewForm'];
  }

  /**
   * Callback for #post_render_cache; replaces placeholder with poll view form.
   *
   * @param int $id
   *   The poll ID.
   * @param string $view_mode
   *   The view mode the poll should be rendered with.
   * @param string $langcode
   *   The langcode in which the poll should be rendered.
   *
   * @return array
   *   A renderable array containing the poll form.
   */
  public function renderViewForm($id, $view_mode, $langcode = NULL) {
    /** @var \Drupal\poll\PollInterface $poll */
    $poll = $this->entityTypeManager->getStorage('poll')->load($id);

    if ($poll) {
      if ($langcode && $poll->hasTranslation($langcode)) {
        $poll = $poll->getTranslation($langcode);
      }
      /** @var Drupal\poll\Form\PollViewForm $form_object */
      $form_object = $this->classResolver->getInstanceFromDefinition(PollViewForm::class);
      $form_object->setPoll($poll);
      return $this->formBuilder->getForm($form_object, $this->requestStack->getCurrentRequest(), $view_mode);
    }
    else {
      return ['#markup' => ''];
    }
  }

}
