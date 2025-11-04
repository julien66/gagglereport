<?php

namespace Drupal\mailchimp_signup\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\mailchimp\ApiService;
use Drupal\mailchimp_signup\Entity\MailchimpSignup;
use Drupal\mailchimp_signup\Form\MailchimpSignupPageForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'mailchimp_signup_form' formatter.
 *
 * @FieldFormatter(
 *   id = "mailchimp_signup_form",
 *   label = @Translation("Mailchimp signup form"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class MailchimpSignupFormFormatter extends EntityReferenceFormatterBase {
  /**
   * A static counter used to generate the form_id.
   *
   * @var int
   */
  private static $counter = 0;

  /**
   * The mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected ApiService $apiService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setApiService($container->get(ApiService::class));
    return $instance;
  }

  /**
   * Sets the API service.
   */
  public function setApiService(ApiService $apiService): void {
    $this->apiService = $apiService;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if (!empty($item->_loaded)) {
        $entity = $item->entity;
      }

      $signup_id = $entity->id();

      /** @var \Drupal\mailchimp_signup\Entity\MailchimpSignup $signup */
      $signup = mailchimp_signup_load($signup_id);

      $form = new MailchimpSignupPageForm($this->apiService, $this->messenger());

      $form->setFormID($this->getFormId($signup));
      $form->setSignup($signup);

      $elements[$delta] = \Drupal::formBuilder()->getForm($form);
    }

    return $elements;
  }

  /**
   * Get the ID of the form.
   *
   * @param \Drupal\mailchimp_signup\Entity\MailchimpSignup $entity
   *   An instance of the SignUp entity.
   *
   * @return string
   *   Returns the id of the form.
   */
  protected function getFormId(MailchimpSignup $entity) {
    // The base form_id.
    // We keep it the same way as it was until now,
    // without having to add the suffix. We are doing this
    // in case there are already existing form hooks relying
    // on this name, so that we cant at least keep some BC
    // before having to add the suffix for each form coming up next.
    $id = 'mailchimp_signup_subscribe_block_' . $entity->id . '_form';

    // Add the suffix in case we've already created one block
    // with a signup form.
    if (static::$counter && static::$counter >= 1) {
      $id = sprintf('%s_%d', $id, static::$counter);
    }

    static::$counter++;

    return $id;
  }

}
