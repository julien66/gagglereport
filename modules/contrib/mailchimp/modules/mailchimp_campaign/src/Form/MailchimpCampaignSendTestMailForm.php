<?php

namespace Drupal\mailchimp_campaign\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the MailchimpCampaign send campaign test email form.
 *
 * @ingroup mailchimp_campaign
 */
class MailchimpCampaignSendTestMailForm extends EntityConfirmFormBase {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a MailchimpCampaignSendForm object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $system_site_config = \Drupal::config('system.site');
    $site_email = $system_site_config->get('mail');
    $form['testmail'] = [
      '#type' => 'email',
      '#default_value' => $site_email,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to send a test email for %name?',
      ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action will send a test email for the campaign through Mailchimp.');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('mailchimp_campaign.overview');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Send campaign test email');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($testemail = $form_state->getValue('testmail')) {
      if (mailchimp_campaign_sendtestmail_campaign($this->entity, $testemail)) {
        $this->messenger->addStatus($this->t('Mailchimp Campaign %name has been sent a test to %email.',
          [
            '%name' => $this->entity->label(),
            '%email' => $testemail,
          ]));
      }
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   *
   * // @todo Make sure override afterBuild is the correct solution.
   *
   * Have to disable EntityForm::afterbuild for this form.
   * Drupal was attempting to get a field definition for the submit button
   * from the MailchimpCampaign entity, which doesn't (and shouldn't) have it.
   */
  public function afterBuild(array $element, FormStateInterface $form_state) {
    return $element;
  }

}
