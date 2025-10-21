<?php

namespace Drupal\cookies\Form;

use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add and edit an CookiesServiceGroup entity.
 */
class CookiesServiceGroupForm extends EntityForm {

  /**
   * The famous Drupal Cache Tags Invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidator
   */
  protected $cacheTagsInvalidator;

  /**
   * Class constructor.
   */
  public function __construct(CacheTagsInvalidator $cache_tags_invalidator) {
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $cookies_service_group = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group name'),
      '#maxlength' => 255,
      '#default_value' => $cookies_service_group->label(),
      '#description' => $this->t("Only displayed in the COOKiES adminstration."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $cookies_service_group->id(),
      '#machine_name' => [
        'exists' => '\Drupal\cookies\Entity\CookiesServiceGroup::load',
      ],
      '#disabled' => !$cookies_service_group->isNew(),
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Display name'),
      '#maxlength' => 255,
      '#default_value' => $cookies_service_group->get('title'),
      '#description' => $this->t("Displayed name for the tab title."),
      '#required' => TRUE,
    ];

    $form['details'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Details'),
      '#default_value' => $cookies_service_group->get('details'),
      '#description' => $this->t('Displayed as group description.'),
      '#required' => FALSE,
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#min' => 0,
      '#max' => 99,
      '#default_value' => $cookies_service_group->get('weight') ?: 50,
      '#description' => $this->t("Weight for the group order in the cookie documentation."),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $cookies_service_group = $this->entity;
    $status = $cookies_service_group->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Cookie service group.', [
          '%label' => $cookies_service_group->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Cookie service group.', [
          '%label' => $cookies_service_group->label(),
        ]));
    }

    $this->cacheTagsInvalidator->invalidateTags(['config:cookies.cookies_service_group']);
    $form_state->setRedirectUrl($cookies_service_group->toUrl('collection'));
    return $status;
  }

}
