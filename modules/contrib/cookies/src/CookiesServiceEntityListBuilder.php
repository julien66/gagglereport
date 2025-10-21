<?php

namespace Drupal\cookies;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Cookie service entity entities.
 */
class CookiesServiceEntityListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('COOKiES service entity');
    $header['id'] = $this->t('Machine name');
    $header['group'] = $this->t('Service Group');
    $header['consentRequired'] = $this->t('Consent Required');
    $header['enabled'] = $this->t('Enabled');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['group'] = $entity->get('group');
    $row['consentRequired'] = $entity->get('consentRequired') ? $this->t('Enabled') : $this->t('Disabled');
    $row['enabled'] = $entity->get('status') ? $this->t('Enabled') : $this->t('Disabled');
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

}
