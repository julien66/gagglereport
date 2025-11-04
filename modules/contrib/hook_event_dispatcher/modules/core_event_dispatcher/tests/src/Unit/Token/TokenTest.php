<?php

namespace Drupal\Tests\core_event_dispatcher\Unit\Token;

use Drupal\core_event_dispatcher\ValueObject\Token;
use Drupal\Tests\RandomGeneratorTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenTest.
 *
 * @coversDefaultClass \Drupal\core_event_dispatcher\ValueObject\Token
 * @covers ::<!public>
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TokenTest extends TestCase {

  use RandomGeneratorTrait;

  /**
   * @covers ::getType
   */
  public function testTokenType(): void {
    $type = $this->randomString();
    $this->assertEquals($type, Token::create($type, '', '')->getType());
  }

  /**
   * @covers ::getToken
   */
  public function testTokenToken(): void {
    $token = $this->randomString();
    $this->assertEquals($token, Token::create('', $token, '')->getToken());
  }

  /**
   * @covers ::getName
   */
  public function testTokenName(): void {
    $name = $this->randomString();
    $this->assertEquals($name, Token::create('', '', $name)->getName());
  }

  /**
   * @covers ::setDescription
   * @covers ::getDescription
   */
  public function testTokenDescription(): void {
    $description = $this->randomString();
    $this->assertEquals($description, Token::create('', '', '')->setDescription($description)->getDescription());
  }

  /**
   * @covers ::setDynamic
   * @covers ::isDynamic
   *
   * @dataProvider tokenDynamicProvider
   */
  public function testTokenDynamic(bool $dynamic): void {
    $this->assertEquals($dynamic, Token::create('', '', '')->setDynamic($dynamic)->isDynamic());
  }

  public static function tokenDynamicProvider(): \Generator {
    yield [FALSE];
    yield [TRUE];
  }

}
