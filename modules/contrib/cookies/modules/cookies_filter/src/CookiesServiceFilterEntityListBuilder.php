<?php

namespace Drupal\cookies_filter;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Cookie service entity entities.
 */
class CookiesServiceFilterEntityListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('COOKiES service filter entity');
    $header['id'] = $this->t('Machine name');
    $header['service'] = $this->t('Service');
    $header['elementType'] = $this->t('Element type');
    $header['enabled'] = $this->t('Enabled');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['service'] = $entity->get('service');
    $row['elementType'] = $entity->get('elementType');
    $row['enabled'] = $entity->get('status') ? $this->t('Enabled') : $this->t('Disabled');
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

}
