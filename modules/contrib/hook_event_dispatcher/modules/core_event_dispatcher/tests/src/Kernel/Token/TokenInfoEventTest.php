<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Token;

use Drupal\core_event_dispatcher\Event\Token\TokensInfoEvent;
use Drupal\core_event_dispatcher\TokenHookEvents;
use Drupal\core_event_dispatcher\ValueObject\Token;
use Drupal\core_event_dispatcher\ValueObject\TokenType;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Token\TokensInfoEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TokenInfoEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test TokenInfoEvent.
   *
   * @throws \Exception
   */
  public function testTokenInfoEvent(): void {
    $this->listen(TokenHookEvents::TOKEN_INFO, 'onTokenInfo');

    $expectedTypes = [
      'test_type' => [
        'name' => 'Test type',
        'description' => 'Test type desc',
        'needs-data' => NULL,
      ],
      'other_type' => [
        'name' => 'Other type',
        'description' => 'Other type!',
        'needs-data' => 'test_data',
      ],
    ];
    $expectedTokens = [
      'test_type' => [
        'test_token1' => [
          'name' => 'Test name 1',
          'description' => 'Test description 1',
          'dynamic' => FALSE,
        ],
        'test_token2' => [
          'name' => 'Test name 2',
          'description' => 'Test description 2',
          'dynamic' => FALSE,
        ],
      ],
      'other_type' => [
        'test_token3' => [
          'name' => 'Test name 3',
          'description' => NULL,
          'dynamic' => FALSE,
        ],
      ],
      'dynamic_type' => [
        'test_token4' => [
          'name' => 'Test name 4',
          'description' => NULL,
          'dynamic' => TRUE,
        ],
      ],
    ];

    $info = $this->container->get('token')->getInfo();
    $this->assertEquals($expectedTypes, $info['types']);
    $this->assertEquals($expectedTokens, $info['tokens']);
  }

  /**
   * Callback for TokensInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Token\TokensInfoEvent $event
   *   The event.
   */
  public function onTokenInfo(TokensInfoEvent $event): void {
    $types = [
      TokenType::create('test_type', 'Test type')->setDescription('Test type desc'),
      TokenType::create('other_type', 'Other type')->setDescription('Other type!')->setNeedsData('test_data'),
    ];
    $tokens = [
      Token::create('test_type', 'test_token1', 'Test name 1')->setDescription('Test description 1'),
      Token::create('test_type', 'test_token2', 'Test name 2')->setDescription('Test description 2'),
      Token::create('other_type', 'test_token3', 'Test name 3'),
      Token::create('dynamic_type', 'test_token4', 'Test name 4')->setDynamic(TRUE),
    ];

    foreach ($types as $type) {
      $event->addTokenType($type);
    }

    foreach ($tokens as $token) {
      $event->addToken($token);
    }
  }

}
