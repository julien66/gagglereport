<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ViewsEventKernelTestBase.
 */
abstract class ViewsEventKernelTestBase extends KernelTestBase {

  /**
   * The view entity.
   *
   * @var \Drupal\views\ViewEntityInterface
   */
  protected $view;

  /**
   * The views executable.
   *
   * @var \Drupal\views\ViewExecutable
   */
  protected $views;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->view = $this->container->get('entity_type.manager')
      ->getStorage('view')
      ->create([
        'id' => $this->randomMachineName(),
      ]);
    $display = $this->view->getDisplay('default');
    $display['display_options']['pager'] = [
      'type' => 'none',
    ];
    $this->view->set('display', ['default' => $display]);

    $this->views = $this->container->get('views.executable')->get($this->view);
  }

}
