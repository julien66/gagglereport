<?php

namespace Drupal\mailchimp_events;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Mailchimp Events entities.
 *
 * @ingroup mailchimp_events
 */
class MailchimpEventListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    $header['properties'] = $this->t('Properties');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\mailchimp_events\Entity\MailchimpEvent $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mailchimp_event.edit_form',
      ['mailchimp_event' => $entity->id()]
    );

    $properties_label = [];
    foreach ($entity->getProperties() as $property) {
      $properties_label[] = ['#markup' => $property["value"]];
      $properties_label[] = ['#markup' => ', '];
    }
    array_pop($properties_label);
    $row['properties']['data'] = $properties_label;

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t(
        'No @label exist yet.',
        ['@label' => $this->entityType->getPluralLabel()]
    );
    return $build;
  }

}
