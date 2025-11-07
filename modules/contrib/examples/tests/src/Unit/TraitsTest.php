<?php

namespace Drupal\Tests\examples\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for the traits defined in\Drupal\Tests\examples\Unit.
 */
class TraitsTest extends UnitTestCase {

  use FilesTestTrait;
  use RetrieveConfigFilesTrait;
  use RetrieveRoutingFilesTrait;

  /**
   * Provides test data for testNoUuidInConfig().
   *
   * @return array
   *   The test data.
   */
  public static function providerConfigFiles(): array {
    $data = [];
    $directory = self::projectPathname('tests/modules');
    return self::retrieveFiles($directory, 'yml', self::configFilesCallback(...));
  }

  /**
   * Provides test data for testRoutingFilesCallback().
   *
   * @return array
   *   The test data.
   */
  public static function providerRoutingFiles(): array {
    $data = [];
    $directory = self::projectPathname('tests/modules');
    return self::retrieveFiles($directory, 'yml', self::routingFilesCallback(...));
  }

  /**
   * Tests that RetrieveConfigFilesTrait::configFilesCallback() works.
   *
   * @dataProvider providerConfigFilesFiles
   */
  public function testConfigFilesCallback(string $filename, string $path, string $pathname): void {
    $this->assertNotEquals(
      'example_no_config_files_test.yml',
      $filename,
      "$filename is not in the right directory ($path) and should have not been retrieved."
    );
  }

  /**
   * Tests that RetrieveRoutingFilesTrait::routingFilesCallback() works.
   *
   * @dataProvider providerRoutingFiles
   */
  public function testRoutingFilesCallback(string $filename, $path, $pathname): void {
    $this->assertNotEquals(
      'examples_no_module_test.routing.yml',
      $filename,
      "$filename is not a routing file used by a module because $path does not contain any .info.yml file."
    );
  }

}
