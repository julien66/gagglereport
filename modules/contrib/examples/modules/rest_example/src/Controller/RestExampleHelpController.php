<?php

namespace Drupal\rest_example\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\examples\Utility\DescriptionTemplateTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a help page for the REST Examples module.
 *
 * @ingroup rest_example
 */
class RestExampleHelpController implements ContainerInjectionInterface {

  use DescriptionTemplateTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'rest_example';
  }

}
