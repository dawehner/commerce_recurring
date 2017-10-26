<?php

namespace Drupal\commerce_recurring\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\commerce_order\Entity\Order;

/**
 * @AdvancedQueueJobType(
 *   id = "commerce_recurring_order_renew",
 *   label = @Translation("Recurring order renew"),
 * )
 */
class RecurringOrderRenew extends JobTypeBase {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {
    $order = Order::load($job->getPayload()['order_id']);

    if ($subscriptions = $order->get('order_subscriptions')) {
      /** @var \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription */
      foreach ($subscriptions as $subscription) {
        $subscription->getType()->renewRecurringOrder($subscription, $order);
      }
    }
  }

}
