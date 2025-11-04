<?php

namespace Drupal\mailchimp_lists\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clear Mailchimp audience cache.
 */
class MailchimpListsClearCacheForm extends ConfirmFormBase {

  /**
   * The Mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->apiService = $container->get('mailchimp.api');
    $instance->messenger = $container->get('messenger');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mailchimp_lists_admin_clear_cache';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mailchimp_lists.clear_cache'];
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Reset Mailchimp Audience Cache');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('mailchimp_lists.overview');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Confirm clearing of Mailchimp audience cache.');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->apiService->getAudiences([], TRUE);
    $form_state->setRedirectUrl(Url::fromRoute('mailchimp_lists.overview'));
    $this->messenger->addStatus($this->t('Mailchimp audience cache cleared.'));
  }

}
