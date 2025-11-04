<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Token;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\core_event_dispatcher\Event\Token\TokensReplacementAlterEvent;
use Drupal\core_event_dispatcher\TokenHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Token\TokensReplacementAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TokenReplacementAlterEventTest extends KernelTestBase {

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

    $this->metadata = $this->createMock(BubbleableMetadata::class);
    $this->token = $this->container->get('token');
  }

  /**
   * Test the TokensReplacementAlterEvent.
   *
   * @throws \Exception
   */
  public function testTokenReplacementAlterEvent(): void {
    $this->listen(TokenHookEvents::TOKEN_REPLACEMENT_ALTER, 'onTokenReplacementAlter');

    $expectedReplacements = 'Replacement value 1, Replacement value 2, [test_type:token3]';
    $replacements = $this->token->replace(self::MARKUP, self::DATA, self::OPTIONS, $this->metadata);
    $this->assertEquals($expectedReplacements, $replacements);
  }

  /**
   * Callback for TokenReplacementAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Token\TokensReplacementAlterEvent $event
   *   The event.
   */
  public function onTokenReplacementAlter(TokensReplacementAlterEvent $event): void {
    $this->assertSame($this->metadata, $event->getBubbleableMetadata());

    $context = $event->getContext();
    $this->assertEquals(self::TYPE, $context['type']);
    $this->assertEquals(self::TOKENS, $context['tokens']);
    $this->assertEquals(self::DATA, $context['data']);
    $this->assertEquals(self::OPTIONS, $context['options']);

    $replacements = &$event->getReplacements();
    $replacements['[test_type:token1]'] = 'Replacement value 1';
    $replacements['[test_type:token2]'] = 'Replacement value 2';
  }

}
