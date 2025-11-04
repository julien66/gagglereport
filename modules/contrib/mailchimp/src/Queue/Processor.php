<?php

namespace Drupal\mailchimp\Queue;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Queue\QueueFactory;

/**
 * Facilitates processing MailChimp queue items.
 */
class Processor {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Constructs a new Processor object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory, QueueFactory $queue_factory) {
    $this->configFactory = $config_factory;
    $this->queueFactory = $queue_factory;
  }

  /**
   * Processes queue items according to the given or configured limit.
   *
   * @param int $batch_limit
   *   The number of items to processed. If NULL given, the limit set in
   *   configuration will be used.
   *
   * @return int
   *   The number of queue items processed.
   */
  public function process(?int $batch_limit = NULL) {
    $queue = $this->queueFactory->get(MAILCHIMP_QUEUE_CRON);
    $queue->createQueue();
    $queue_count = $queue->numberOfItems();

    if ($queue_count === 0) {
      return 0;
    }

    if (is_null($batch_limit)) {
      $batch_limit = $this->configFactory->get('mailchimp.settings')->get('batch_limit');
    }
    $batch_size = ($queue_count < $batch_limit) ? $queue_count : $batch_limit;

    $processed_count = 0;
    while ($processed_count < $batch_size) {
      if ($item = $queue->claimItem()) {
        call_user_func_array($item->data['function'], $item->data['args']);
        $queue->deleteItem($item);
      }

      $processed_count++;
    }

    return $processed_count;
  }

}
