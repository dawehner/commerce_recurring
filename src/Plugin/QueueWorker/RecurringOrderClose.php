<?php

namespace Drupal\commerce_recurring\Plugin\QueueWorker;

use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Queue\QueueWorkerInterface;

/**
 * @QueueWorker(
 *   id = "commerce_recurring_order_close",
 *   title = "Recurring order closing",
 *   cron = {"time" = 60},
 * )
 */
class RecurringOrderClose extends PluginBase implements QueueWorkerInterface {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $order = Order::load($data['order_id']);

    // Somehow leverage usage groups to be able to determine whether the order
    // can be closed.
  }

}
