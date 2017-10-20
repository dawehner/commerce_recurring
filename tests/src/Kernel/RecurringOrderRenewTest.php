<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\advancedqueue\Entity\Queue;
use Drupal\commerce_price\Price;
use Drupal\commerce_recurring\RecurringCron;
use Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange;
use Drupal\Component\Datetime\TimeInterface;

/**
 * Tests the logic to determine which orders should be refreshed.
 *
 * @group commerce_recurring
 */
class RecurringOrderRenewTest extends CommerceRecurringKernelTestBase {
  
  protected function setUp() {
    parent::setUp(); // TODO: Change the autogenerated stub

    \Drupal::getContainer()->set('datetime.time', new CustomTime(\Drupal::time()->getRequestTime()));
  }

  /**
   * Tests the logic to fill up the recurring order queue for refresh and close.
   */
  public function testRecurringOrderRefreshQueue() {
    list($subscription, $order) = $this->createBasicSubscriptionAndOrder();

    // Ensure the refresh queue is empty.
    $this->assertEquals(0, \Drupal::queue('commerce_recurring_refresh')->numberOfItems());

    // Fast forward in time and run cron.
    
    \Drupal::time()->setTime($subscription->get('started')->value + 100);
    // We don't trigger the cron directly as this processes the queue items
    // already.
    RecurringCron::create(\Drupal::getContainer())->cron();

    /** @var \Drupal\advancedqueue\Entity\QueueInterface $queue */
    $queue = Queue::load('commerce_recurring');
    $this->assertEquals(['queued' => 2, 'processing' => 0,'success' => 0, 'failure' => 0], $queue->getBackend()->countJobs());

    $job1 = $queue->getBackend()->claimJob();
    $job2 = $queue->getBackend()->claimJob();

    $this->assertArraySubset(['order_id' => $order->id()], $job1->getPayload());
    $this->assertEquals('commerce_recurring_order_close', $job1->getType());
    $this->assertArraySubset(['order_id' => $order->id()], $job2->getPayload());
    $this->assertEquals('commerce_recurring_order_renew', $job2->getType());
  }

  /**
   * Tests the actual logic of recurring a recurring order.
   */
  public function testRecurringOrderRefreshLogic() {
    list($subscription, $order) = $this->createBasicSubscriptionAndOrder();

    /** @var \Drupal\commerce_order\Entity\OrderInterface $next_order */
    $next_order = $subscription->getType()->renewRecurringOrder($subscription, $order);
    $this->assertNotEquals($next_order->id(), $order->id());

    $this->assertEquals('recurring', $next_order->bundle());
    $this->assertEquals(\Drupal::time()->getRequestTime() + 45, $next_order->get('started')->value);
    $this->assertEquals(\Drupal::time()->getRequestTime() + 95, $next_order->get('ended')->value);
    $this->assertCount(1, $next_order->getItems());
    $this->assertEquals(2, $next_order->getItems()[0]->getUnitPrice()->getNumber());
    $this->assertEquals('recurring', $next_order->getItems()[0]->bundle());
    $this->assertEquals(1, $next_order->getItems()[0]->getQuantity());
  }

  public function testScheduleChanges() {
    list ($subscription, $order) = $this->createBasicSubscriptionAndOrder();

    // Add a subscription change which changes everything.
    /** @var \Drupal\commerce_recurring\SubscriptionChange\SubscriptionChangeManagerInterface $change_manager */
    $change_manager = \Drupal::service('commerce_recurring.subscription_changes.manager');

    $change_manager->addScheduledChange(new SubscriptionChange($subscription->id(), 'amount', new Price(3, 'USD')));

    /** @var \Drupal\commerce_order\Entity\OrderInterface $next_order */
    $next_order = $subscription->getType()->renewRecurringOrder($subscription, $order);
    $this->assertNotEquals($next_order->id(), $order->id());

    $this->assertEquals(3, $next_order->getTotalPrice()->getNumber());
  }

}

class CustomTime implements TimeInterface {

  /**
   * @var int
   */
  protected $time;

  /**
   * CustomTime constructor.
   * @param int $time
   */
  public function __construct($time) {
    $this->time = $time;
  }

  /**
   * @param int $time
   */
  public function setTime($time) {
    $this->time = $time;
  }

  public function getRequestTime() {
    return $this->time;
  }

  public function getRequestMicroTime() {
    return $this->time;
  }

  public function getCurrentTime() {
    return $this->time;
  }

  public function getCurrentMicroTime() {
    return $this->time;
  }

}
