<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_payment\Entity\Payment;
use Drupal\commerce_recurring\Entity\Subscription;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the creationg of subscription entities when an order got placed.
 */
class SubscriptionCreationTest extends CommerceRecurringKernelTestBase {

  /**
   * Tests that subscriptions are created when completing an order with
   * subscribeable entities.
   */
  public function testCompleteOrder() {
    $currentUser = $this->createUser([], []);
    \Drupal::currentUser()->setAccount($currentUser);

    $order_item = OrderItem::create([
      'type' => 'with_subscriptions',
      'purchased_entity' => $this->variation,
      'total_price' => $this->product
    ]);
    $order_item->save();

    $order = Order::create([
      'type' => 'with_subscriptions',
      'state' => 'draft',
      'payment_method' => $this->paymentMethod,
    ]);
    $order->setCustomer($currentUser);
    $order->setStore($this->store);
    $order->addItem($order_item);
    $order->save();

    Payment::create([
      'type' => 'payment_default',
      'payment_gateway' => $this->paymentGateway,
      'payment_method' => $this->paymentMethod,
      'order_id' => $order,
      'amount' => $this->variation->getPrice(),
    ])->save();

    $order->getState()->applyTransition($order->getState()->getTransitions()['place']);

    $subscriptions = Subscription::loadMultiple();
    $this->assertCount(0, $subscriptions);

    $order->save();

    $subscriptions = Subscription::loadMultiple();
    $this->assertCount(1, $subscriptions);
    /** @var \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription */
    $subscription = reset($subscriptions);

    $this->assertEquals($this->paymentMethod->id(), $subscription->getPaymentMethod()->id());
    $this->assertEquals($this->variation->id(), $subscription->getPurchasedEntity()->id());
    $this->assertEquals($this->billingSchedule->id(), $subscription->getBillingSchedule()->id());
    $this->assertEquals($currentUser->id(), $subscription->getCustomer()->id());
    $this->assertEquals(10, (int) $subscription->getAmount()->getNumber());
  }

}
