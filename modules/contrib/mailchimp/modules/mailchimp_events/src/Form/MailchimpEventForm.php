<?php

namespace Drupal\mailchimp_events\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Mailchimp Event edit forms.
 *
 * @ingroup mailchimp_events
 */
class MailchimpEventForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailchimp_events\Entity\MailchimpEvent $entity */
    $form = parent::buildForm($form, $form_state);

    $form["name"]["widget"][0]["value"]["#type"] = 'machine_name';
    $form["name"]["widget"][0]["value"]['#machine_name'] = [
      'exists' => [$this, 'exists'],
    ];

    foreach (Element::getVisibleChildren($form['properties']['widget']) as $key) {
      if (isset($form['properties']['widget'][$key]["value"]["#type"]) && $form['properties']['widget'][$key]["value"]["#type"] == 'textfield') {
        $form['properties']['widget'][$key]["value"]["#type"] = 'machine_name';
        $form['properties']['widget'][$key]["value"]['#machine_name'] = [
          'exists' => [$this, 'exists'],
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label event type.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label event type.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.mailchimp_event.collection');
  }

  /**
   * Ignores the exist requirement for the machine_name setting.
   *
   * @param string $id
   *   The name.
   *
   * @return bool
   *   FALSE because we don't actually care if these are unique.
   */
  public function exists($id) {
    return FALSE;
  }

}
