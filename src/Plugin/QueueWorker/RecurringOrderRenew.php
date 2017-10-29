<?php

namespace Drupal\commerce_recurring\Plugin\QueueWorker;

use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Queue\QueueWorkerInterface;

/**
 * @QueueWorker(
 *   id = "commerce_recurring_order_renew",
 *   title = "Recurring order renew",
 *   cron = {"time" = 60},
 * )
 */
class RecurringOrderRenew extends PluginBase implements QueueWorkerInterface {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $order = Order::load($data['order_id']);

    if (!$subscriptions = $order->get('order_subscriptions')) {
      /** @var \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription */
      foreach ($subscriptions as $subscription) {
        $subscription->getType()->renewRecurringOrder($subscription, $order);
      }
    }
  }

}
