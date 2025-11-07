<?php

namespace Drupal\Tests\examples\Unit;

use Drupal\Tests\UnitTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Verifies route paths are valid.
 *
 * Route paths are considered valid when they start with a slash.
 *
 * @group examples
 */
class RoutePathTest extends UnitTestCase {

  use FilesTestTrait;
  use RetrieveRoutingFilesTrait;

  /**
   * Provides test data for testPathStartsWithSlash().
   *
   * @return array
   *   The test data.
   */
  public static function providerRoutingFiles(): array {
    $data = [];
    $directory = self::projectPathname('modules');
    return self::retrieveFiles($directory, 'yml', self::routingFilesCallback(...));
  }

  /**
   * Tests that route paths start with a slash.
   *
   * @dataProvider providerRoutingFiles
   */
  public function testPathStartsWithSlash(string $filename, string $path, string $pathname): void {
    $routes = Yaml::parse(file_get_contents($pathname));

    foreach ($routes as $name => $route) {
      if (isset($route['path'])) {
        $this->assertEquals('/', $route['path'][0], "$filename in $path contains a route ($name) that does not start with a slash.");
      }
    }
  }

}
