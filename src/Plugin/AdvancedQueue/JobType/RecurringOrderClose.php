<?php

namespace Drupal\commerce_recurring\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\commerce_order\Entity\Order;

/**
 * @AdvancedQueueJobType(
 *   id = "commerce_recurring_order_close",
 *   label = @Translation("Recurring order closing"),
 * )
 */
class RecurringOrderClose extends JobTypeBase {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load($job->getPayload()['order_id']);

    $order->getState()->applyTransition($order->getState()['cancel']);
    $order->save();
  }

}
