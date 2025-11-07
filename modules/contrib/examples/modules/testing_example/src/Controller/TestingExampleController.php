<?php

namespace Drupal\testing_example\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\examples\Utility\DescriptionTemplateTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for testing_example module.
 *
 * This class uses the DescriptionTemplateTrait to display text we put in the
 * templates/description.html.twig file.  We render out the text via its
 * description() method, and set up our routing to point to
 * TestingExampleController::description().
 */
class TestingExampleController implements ContainerInjectionInterface {

  use DescriptionTemplateTrait;

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * Constructs a new \Drupal\testing_example\Controller\TestingExampleController.
   *
   * @param \Drupal\Core\Extension\ModuleExtensionList $module_extension
   *   The module extension list.
   */
  public function __construct(ModuleExtensionList $module_extension) {
    $this->moduleExtensionList = $module_extension;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('extension.list.module'));
  }

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'testing_example';
  }

  /**
   * Generate a render array for the Simpletest description.
   *
   * @return array
   *   A render array.
   */
  public function simpletestDescription() {
    $template_file = $this->moduleExtensionList->getPath('testing_example') . '/templates/simpletest.description.html.twig';
    $build = [
      'description' => [
        '#type' => 'inline_template',
        '#template' => file_get_contents($template_file),
      ],
    ];

    return $build;
  }

}
