<?php

namespace Drupal\Tests\preprocess_event_dispatcher\Unit;

use Drupal\Core\Entity\EntityInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait EntityMockTrait {

  /**
   * Get a full Entity mock.
   *
   * @param class-string<T> $class
   *   Class of mocked entity.
   * @param string $type
   *   Entity type.
   * @param string $bundle
   *   Entity bundle.
   *
   * @template T of \Drupal\Core\Entity\EntityInterface
   */
  protected function getMock(string $class, string $type, string $bundle): MockObject&EntityInterface {
    /** @var \PHPUnit\Framework\MockObject\MockObject&T $mock */
    $mock = $this->createMock($class);
    $mock->method('getEntityTypeId')->willReturn($type);
    $mock->method('bundle')->willReturn($bundle);

    return $mock;
  }

}
