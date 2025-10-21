<?php

namespace Drupal\poll;

use Drupal\content_translation\ContentTranslationMetadataWrapper;
use Drupal\user\UserInterface;

/**
 * Base class for content translation metadata wrappers.
 */
class PollChoiceTranslationMetadataWrapper extends ContentTranslationMetadataWrapper {

  /**
   * {@inheritdoc}
   */
  public function getSource() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setSource($source) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isOutdated() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setOutdated($outdated) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthor(UserInterface $account) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function setFieldOnlyIfTranslatable($field_name, $value) {}

}
