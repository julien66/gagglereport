<?php

declare(strict_types=1);

namespace Drupal\Tests\mailchimp\Kernel;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Language\ContextProvider\CurrentLanguageContext;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\mailchimp\ApiService;
use Drupal\mailchimp\ClientFactory;
use Mailchimp\Tests\Mailchimp as TestMailchimp;
use Mailchimp\Tests\MailchimpLists as TestMailchimpLists;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test description.
 */
#[Group('mailchimp')]
final class ApiServiceTest extends MailchimpKernelTestBase {

  /**
   * The Api service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * The key-value factory.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $keyValueFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $api_class = new TestMailchimp(['api_key' => 'TEST', 'api_user' => 'TEST']);
    $client_factory = $this->prophesize(ClientFactory::class);
    $mc_lists = new TestMailchimpLists($api_class);
    $client_factory->getByClassNameOrNull('MailchimpLists')->willReturn($mc_lists);
    $messenger = $this->prophesize(MessengerInterface::class);
    $key_value_store = $this->prophesize(KeyValueStoreInterface::class);
    $this->keyValueFactory = $this->prophesize(KeyValueFactoryInterface::class);
    $this->keyValueFactory->get('mailchimp_lists')->willReturn($key_value_store->reveal());
    $cache = $this->prophesize(CacheBackendInterface::class);
    $logger = $this->prophesize(LoggerChannelInterface::class);
    $languageContext = $this->prophesize(CurrentLanguageContext::class);
    $moduleHandler = $this->prophesize(ModuleHandlerInterface::class);
    $configFactory = $this->prophesize(ConfigFactoryInterface::class);
    $config = $this->prophesize(ImmutableConfig::class);
    $config->get('test_mode')->willReturn(TRUE);
    $configFactory->get('mailchimp.settings')->willReturn($config->reveal());
    $this->apiService = new ApiService(
      $client_factory->reveal(),
      $messenger->reveal(),
      $this->keyValueFactory->reveal(),
      $cache->reveal(),
      $logger->reveal(),
      $languageContext->reveal(),
      $moduleHandler->reveal(),
      $configFactory->reveal()
    );
  }

  /**
   * Tests the API service's getMergeVars() function.
   */
  public function testGetMergevars(): void {
    $list_ids = ['57afe96172', '587693d673'];
    $result = $this->apiService->getMergevars($list_ids);
    self::assertCount(2, $result, "getMergevars() returned incorrect number of lists.");
    foreach ($list_ids as $list_id) {
      self::assertCount(4, $result[$list_id], "$list_id returned unexpected mergevar count.");
      self::assertSame('EMAIL', $result[$list_id][0]->tag, "$list_id returned unexpected mergevar tag.");
      self::assertSame('FNAME', $result[$list_id][1]->tag, "$list_id returned unexpected mergevar tag.");
      self::assertSame('LNAME', $result[$list_id][2]->tag, "$list_id returned unexpected mergevar tag.");
    }
  }

}
