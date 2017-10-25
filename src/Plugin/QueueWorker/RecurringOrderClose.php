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
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load($data['order_id']);

    $order->getState()->applyTransition($order->getState()['cancel']);
    $order->save();
  }

}
