<?php

namespace Drupal\config_simple_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\examples\Utility\DescriptionTemplateTrait;

/**
 * Controller routines for Config Simple Example routes.
 */
class ConfigSimpleController extends ControllerBase {

  use DescriptionTemplateTrait;

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'config_simple_example';
  }

}
