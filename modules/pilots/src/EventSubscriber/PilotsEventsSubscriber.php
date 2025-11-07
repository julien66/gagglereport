<?php
namespace Drupal\pilots\EventSubscriber;

use Drupal\core_event_dispatcher\FormHookEvents;
use Drupal\core_event_dispatcher\Event\Form\FormAlterEvent;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/*
 */

class PilotsEventsSubscriber implements EventSubscriberInterface {
  /**
   * Entity pre save.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   The event.
   */
  public function entityPreSave(EntityPresaveEvent $event): void {
      $entity = $event->getEntity();
      if ($entity->getEntityTypeId() == "user") {
	  $civlid = $entity->get('field_civlid')->value;
	  if (!empty($civlid)) {
		  $rank = \Drupal::service('pilots.wprs_forecast_call')->getCurrentRank($civlid);
		  if ($rank > 0) {
		      $entity->set('field_wprs', $rank);
		  }
		  $bestRank = \Drupal::service('pilots.wprs_forecast_call')->getBestRank($civlid);
		  if ($bestRank > 0) {
		      $entity->set('field_best_wprs', $bestRank);
		  }
          }			
      }
  }

  /**
   * Alter form.
   *
   * @param \Drupal\core_event_dispatcher\Event\Form\FormAlterEvent $event
   *   The event.
   */
  public function alterForm(FormAlterEvent $event): void {
      $form = &$event->getForm();
      if ($form['#id'] == "user-form") {
          $form['field_wprs']['#disabled'] = True;
          $form['field_best_wprs']['#disabled'] = True;
      }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
	    EntityHookEvents::ENTITY_PRE_SAVE => 'entityPreSave',
      	    FormHookEvents::FORM_ALTER => 'alterForm',
   ];
  }
}
