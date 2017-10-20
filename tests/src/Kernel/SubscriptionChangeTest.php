<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_price\Price;
use Drupal\commerce_recurring\Entity\Subscription;
use Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange;
use Drupal\commerce_recurring\SubscriptionChange\SubscriptionChangeManager;

/**
 * Tests subscription changes.
 *
 * @group commerce_recurring
 */
class SubscriptionChangeTest extends CommerceRecurringKernelTestBase {

  public function testCrud() {
    list ($subscription, $order) = $this->createBasicSubscriptionAndOrder();

    $change_manager = new SubscriptionChangeManager();
    $change_manager->addScheduledChange(new SubscriptionChange($subscription->id(), 'amount', new Price(3, 'USD')));

    $changes = $change_manager->getChangesPerSubscription($subscription);
    $this->assertCount(1, $changes);
    $this->assertEquals('amount', $changes[0]->getFieldName());
    $this->assertEquals(new Price(3, 'USD'), $changes[0]->getValue());

    $change_manager->applyChanges($subscription, $changes);
    $subscription = Subscription::load($subscription->id());
    
    $this->assertEquals(3, $subscription->getAmount()->getNumber());
  }

}
