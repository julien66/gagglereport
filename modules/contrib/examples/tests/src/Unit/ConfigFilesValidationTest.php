<?php

namespace Drupal\Tests\examples\Unit;

use Drupal\Tests\UnitTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Verifies the configuration files are valid.
 *
 * Configuration files are considered valid if they do not contain an uuid key.
 *
 * @group examples
 */
class ConfigFilesValidationTest extends UnitTestCase {

  use FilesTestTrait;
  use RetrieveConfigFilesTrait;

  /**
   * Provides test data for testNoUuidInConfig().
   *
   * @return array
   *   The test data.
   */
  public static function providerConfigFiles(): array {
    $data = [];
    $directory = self::projectPathname('modules');
    return self::retrieveFiles($directory, 'yml', self::configFilesCallback(...));
  }

  /**
   * Tests that the configuration files do not contain any uuid key.
   *
   * @dataProvider providerConfigFiles
   */
  public function testNoUuidInConfig(string $filename, string $path, string $pathname): void {
    $yaml = Yaml::parse(file_get_contents($pathname));
    $this->assertArrayNotHasKey('uuid', $yaml, "$filename in $path contains an uuid key.");
  }

}
