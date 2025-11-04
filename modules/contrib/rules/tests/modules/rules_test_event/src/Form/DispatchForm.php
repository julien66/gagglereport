<?php

namespace Drupal\rules_test_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules_test_event\Event\PlainEvent;
use Drupal\rules_test_event\Event\GenericEvent;
use Drupal\rules_test_event\Event\GetterEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Acquires input, wraps it in a Task object, and queues it for processing.
 */
class DispatchForm extends FormBase {

  /**
   * The event_dispatcher service.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Constructor.
   *
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event_dispatcher service.
   */
  public function __construct(EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rules_test_event.dispatcher_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['event'] = [
      '#type' => 'radios',
      '#options' => [
        0 => $this->t('PlainEvent'),
        1 => $this->t('GenericEvent'),
        2 => $this->t('GetterEvent'),
      ],
      '#description' => $this->t('Choose Event to dispatch for testing.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Dispatch event',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $choice = $form_state->getValue('event');
    var_dump($choice);
    switch ($choice) {
      case 0:
        $event = new PlainEvent();
        break;

      case 1:
        $event = new GenericEvent('Test subject', [
          'publicProperty' => 'public property',
          'protectedProperty' => 'protected property',
          'privateProperty' => 'private property',
        ]);
        break;

      case 2:
        $event = new GetterEvent();
        break;
    }

    $this->dispatcher->dispatch($event, $event::EVENT_NAME);
  }

}
