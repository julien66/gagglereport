<?php

namespace Drupal\mailchimp_lists\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Batch update Mailchimp audience mergevars.
 */
class MailchimpListsUpdateMergevarsForm extends ConfirmFormBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * MailchimpListsUpdateMergevarsForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The current request stack.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(RequestStack $request_stack, MessengerInterface $messenger) {
    $this->requestStack = $request_stack;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mailchimp_lists_admin_update_mergevars';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mailchimp_lists.update_mergevars'];
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Update mergevars on all entities?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('mailchimp_lists.fields');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This can overwrite values configured directly on your Mailchimp Account.');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $request = $this->requestStack->getCurrentRequest();
    $entity_type = $request->get('entity_type');
    $bundle = $request->get('bundle');
    $field_name = $request->get('field_name');

    mailchimp_lists_update_member_merge_values($entity_type, $bundle, $field_name);

    $this->messenger->addStatus($this->t('Mergevars updated.'));
  }

}
