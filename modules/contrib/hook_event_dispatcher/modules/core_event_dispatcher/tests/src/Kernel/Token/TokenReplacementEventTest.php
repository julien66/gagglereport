<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Token;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\core_event_dispatcher\Event\Token\TokensReplacementEvent;
use Drupal\core_event_dispatcher\TokenHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Token\TokensReplacementEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TokenReplacementEventTest extends KernelTestBase {

  use ListenerTrait;

  protected const TYPE = 'test_type';

  protected const TOKENS = [
    'token1' => '[test_type:token1]',
    'token2' => '[test_type:token2]',
    'token3' => '[test_type:token3]',
  ];

  protected const DATA = [
    'test_data' => 'test!',
  ];

  protected const MARKUP = '[test_type:token1], [test_type:token2], [test_type:token3]';

  protected const OPTIONS = [
    'test_options' => 'Option value',
  ];

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The bubbleable metadata.
   *
   * @var \Drupal\Core\Render\BubbleableMetadata|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $metadata;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->eventDispatcher = $this->container->get('event_dispatcher');
    $this->token = $this->container->get('token');
    $this->metadata = $this->createMock(BubbleableMetadata::class);
  }

  /**
   * Test TokenReplacementEvent.
   *
   * @throws \Exception
   */
  public function testTokenReplacementEvent(): void {
    $this->listen(TokenHookEvents::TOKEN_REPLACEMENT, 'onTokenReplacement');

    $expectedReplacements = 'Replacement value 1, Replacement value 2, [test_type:token3]';

    $replacements = $this->token->replace(self::MARKUP, self::DATA, self::OPTIONS, $this->metadata);
    $this->assertEquals($expectedReplacements, $replacements);
  }

  /**
   * Callback for TokensReplacementEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Token\TokensReplacementEvent $event
   *   The event.
   */
  public function onTokenReplacement(TokensReplacementEvent $event): void {
    $this->assertEquals(self::TYPE, $event->getType());
    $this->assertEquals(self::TOKENS, $event->getTokens());
    $this->assertEquals(self::DATA, $event->getRawData());
    $this->assertEquals(self::DATA['test_data'], $event->getData('test_data'));
    $this->assertEquals(self::OPTIONS, $event->getOptions());
    $this->assertSame($this->metadata, $event->getBubbleableMetadata());

    $event->setReplacementValue('test_type', 'token1', 'Replacement value 1');
    $event->setReplacementValue('test_type', 'token2', 'Replacement value 2');
  }

  /**
   * Test TokenReplacementEvent wrong replacement exception.
   */
  public function testTokenReplacementEventWrongReplacementException(): void {
    $this->expectException(\UnexpectedValueException::class);

    $this->eventDispatcher->addListener(TokenHookEvents::TOKEN_REPLACEMENT, static function (TokensReplacementEvent $event) {
      $event->setReplacementValue('', '', '');
    });

    $this->token->replace(self::MARKUP, [], [], $this->metadata);
  }

  /**
   * Test TokenReplacementEvent invalid replacement Exception.
   */
  public function testTokenReplacementEventInvalidReplacementException(): void {
    $this->expectException(\UnexpectedValueException::class);

    $this->eventDispatcher->addListener(TokenHookEvents::TOKEN_REPLACEMENT, static function (TokensReplacementEvent $event) {
      $event->setReplacementValue('test', 'token', NULL);
    });

    $this->token->replace(self::MARKUP, [], [], $this->metadata);
  }

  public function testTokenReplacementEventWithNonStringTokenType(): void {
    $markup = self::MARKUP . ', [1:1], [0.5:0.5]';
    $replacements = $this->token->replace($markup, self::DATA, self::OPTIONS, $this->metadata);
    $this->assertEquals($markup, $replacements);
  }

}
