<?php

namespace Drupal\Tests\core_event_dispatcher\Unit\Token;

use Drupal\core_event_dispatcher\ValueObject\TokenType;
use Drupal\Tests\RandomGeneratorTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenTypeTest.
 *
 * @coversDefaultClass \Drupal\core_event_dispatcher\ValueObject\TokenType
 * @covers ::<!public>
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TokenTypeTest extends TestCase {

  use RandomGeneratorTrait;

  /**
   * @covers ::getType
   */
  public function testTokenType(): void {
    $type = $this->randomString();
    $this->assertEquals($type, TokenType::create($type, '')->getType());
  }

  /**
   * @covers ::getName
   */
  public function testTokenName(): void {
    $name = $this->randomString();
    $this->assertEquals($name, TokenType::create('', $name)->getName());
  }

  /**
   * @covers ::setDescription
   * @covers ::getDescription
   */
  public function testDescription(): void {
    $description = $this->randomString();
    $this->assertEquals($description, TokenType::create('', '')->setDescription($description)->getDescription());
  }

  /**
   * @covers ::setNeedsData
   * @covers ::getNeedsData
   */
  public function testNeedsData(): void {
    $needsData = $this->randomString();
    $this->assertEquals($needsData, TokenType::create('', '')->setNeedsData($needsData)->getNeedsData());
  }

}
