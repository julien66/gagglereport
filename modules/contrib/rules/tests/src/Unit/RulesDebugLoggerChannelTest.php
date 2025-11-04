<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\UnitTestCase;
use Drupal\rules\Logger\RulesDebugLog;
use Drupal\rules\Logger\RulesDebugLoggerChannel;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

// cspell:ignore dragonfruit

/**
 * @coversDefaultClass \Drupal\rules\Logger\RulesDebugLoggerChannel
 * @group Rules
 */
class RulesDebugLoggerChannelTest extends UnitTestCase {

  /**
   * The Drupal service container.
   *
   * @var \Drupal\Core\DependencyInjection\Container
   */
  protected $container;

  /**
   * The session service.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * The Rules logger.channel.rules_debug service.
   *
   * @var \Psr\Log\LoggerChannelInterface
   */
  protected $rulesDebugLogger;

  /**
   * The Rules logger.rules_debug_log service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $rulesDebugLog;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $container = new ContainerBuilder();
    $this->rulesDebugLogger = $this->prophesize(LoggerChannelInterface::class)->reveal();
    $container->set('logger.channel.rules_debug', $this->rulesDebugLogger);
    $this->session = new Session(new MockArraySessionStorage());
    $container->set('session', $this->session);

    $this->rulesDebugLog = new RulesDebugLog($this->session);
    $container->set('logger.rules_debug_log', $this->rulesDebugLog);
    \Drupal::setContainer($container);
    $this->container = $container;
  }

  /**
   * Tests LoggerChannel::log().
   *
   * @param string $psr3_message_level
   *   Expected PSR3 log level.
   * @param int $rfc_message_level
   *   Expected RFC 5424 log level.
   * @param bool $system_debug_enabled
   *   Is system debug logging enabled.
   * @param bool $debug_log_enabled
   *   Is debug logging enabled.
   * @param string $psr3_log_error_level
   *   Minimum required PSR3 log level at which to log.
   * @param int $expected_system_logs
   *   Number of logs expected to be created.
   * @param int $expected_screen_logs
   *   Number of messages expected to be created.
   * @param string $message
   *   Log message.
   *
   * @dataProvider providerTestLog
   *
   * @covers ::log
   */
  public function testLog(string $psr3_message_level, int $rfc_message_level, bool $system_debug_enabled, bool $debug_log_enabled, string $psr3_log_error_level, int $expected_system_logs, int $expected_screen_logs, string $message): void {
    // Clean up after previous test.
    $this->rulesDebugLog->clearLogs();

    $config = $this->getConfigFactoryStub([
      'rules.settings' => [
        'system_log' => [
          'log_level' => $psr3_log_error_level,
        ],
        'debug_log' => [
          'enabled' => $debug_log_enabled,
          'system_debug' => $system_debug_enabled,
          'log_level' => $psr3_log_error_level,
        ],
      ],
    ]);

    $channel = new RulesDebugLoggerChannel($this->rulesDebugLog, $config);
    $addedLogger = $this->prophesize(LoggerInterface::class);
    $addedLogger->log($rfc_message_level, $message, Argument::type('array'))
      ->shouldBeCalledTimes($expected_screen_logs);

    $channel->addLogger($addedLogger->reveal());
    $channel->log($psr3_message_level, $message, []);

    $messages = $this->rulesDebugLog->getLogs();
    if ($expected_screen_logs > 0) {
      $this->assertNotNull($messages);
      $context = [
        'channel' => 'rules_debug',
        'link' => '',
        'element' => NULL,
        'scope' => NULL,
        'path' => NULL,
      ];
      $context += $messages[0]['context'];
      $this->assertEquals([
        0 => [
          'message' => $message,
          'context' => $context,
          'level' => $psr3_message_level,
          'timestamp' => $context['timestamp'],
          'scope' => NULL,
          'path' => NULL,
        ],
      ], $messages, "actual =" . var_export($messages, TRUE));
    }
    else {
      $this->assertCount(0, $messages);
    }
  }

  /**
   * Data provider for self::testLog().
   */
  public static function providerTestLog(): array {
    return [
      [
        'psr3_message_level' => LogLevel::DEBUG,
        'rfc_message_level' => RfcLogLevel::DEBUG,
        'system_debug_enabled' => FALSE,
        'debug_log_enabled' => FALSE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 0,
        'expected_screen_logs' => 0,
        'message' => 'apple',
      ],
      [
        'psr3_message_level' => LogLevel::DEBUG,
        'rfc_message_level' => RfcLogLevel::DEBUG,
        'system_debug_enabled' => FALSE,
        'debug_log_enabled' => TRUE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 0,
        'expected_screen_logs' => 1,
        'message' => 'pear',
      ],
      [
        'psr3_message_level' => LogLevel::CRITICAL,
        'rfc_message_level' => RfcLogLevel::CRITICAL,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => FALSE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 1,
        'expected_screen_logs' => 0,
        'message' => 'banana',
      ],
      [
        'psr3_message_level' => LogLevel::CRITICAL,
        'rfc_message_level' => RfcLogLevel::CRITICAL,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => TRUE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 1,
        'expected_screen_logs' => 1,
        'message' => 'carrot',
      ],
      [
        'psr3_message_level' => LogLevel::CRITICAL,
        'rfc_message_level' => RfcLogLevel::CRITICAL,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => FALSE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 1,
        'expected_screen_logs' => 0,
        'message' => 'orange',
      ],
      [
        'psr3_message_level' => LogLevel::CRITICAL,
        'rfc_message_level' => RfcLogLevel::CRITICAL,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => TRUE,
        'psr3_log_error_level' => LogLevel::DEBUG,
        'expected_system_logs' => 1,
        'expected_screen_logs' => 1,
        'message' => 'kumquat',
      ],
      [
        'psr3_message_level' => LogLevel::INFO,
        'rfc_message_level' => RfcLogLevel::INFO,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => FALSE,
        'psr3_log_error_level' => LogLevel::CRITICAL,
        'expected_system_logs' => 0,
        'expected_screen_logs' => 0,
        'message' => 'cucumber',
      ],
      [
        'psr3_message_level' => LogLevel::INFO,
        'rfc_message_level' => RfcLogLevel::INFO,
        'system_debug_enabled' => TRUE,
        'debug_log_enabled' => TRUE,
        'psr3_log_error_level' => LogLevel::CRITICAL,
        'expected_system_logs' => 0,
        'expected_screen_logs' => 0,
        'message' => 'dragonfruit',
      ],
    ];
  }

}
