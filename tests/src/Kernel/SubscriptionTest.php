<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\commerce_payment\Entity\PaymentMethod;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\commerce_recurring\Entity\Subscription;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests subscription entities.
 *
 * @group commerce_recurring
 */
class SubscriptionTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce',
    'commerce_payment',
    'commerce_payment_example',
    'commerce_price',
    'commerce_product',
    'commerce_recurring',
    'commerce_recurring_test',
    'entity_reference_revisions',
    'profile',
    'state_machine',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('commerce_subscription');
    $this->installEntitySchema('commerce_payment_method');
    $this->installEntitySchema('commerce_product_variation');
  }

  public function testSubscriptionPlugins() {
    /** @var \Drupal\commerce_recurring\SubscriptionTypeManager $subscription_type_manager */
    $subscription_type_manager = \Drupal::service('plugin.manager.commerce_subscription_type');
    $definitions = $subscription_type_manager->getDefinitions();
    $this->assertArrayHasKey('license', $definitions);
  }

  public function testCrudEntity() {
    $billing_schedule = BillingSchedule::create([
      'id' => 'test_id',
      'label' => 'Test label',
      'display_label' => 'Test customer label',
      'plugin' => 'test_plugin',
      'configuration' => [
        'key' => 'value',
      ],
    ]);
    $billing_schedule->save();

    /** @var \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method */
    $payment_gateway = PaymentGateway::create([
      'id' => 'example',
      'label' => 'Example',
      'plugin' => 'example_onsite',
    ]);
    $payment_gateway->save();

    /** @var \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method */
    $payment_method = PaymentMethod::create([
      'type' => 'credit_card',
      'payment_gateway' => $payment_gateway,
    ]);
    $payment_method->save();

    ProductVariationType::create([
      'id' => 'default',
      'label' => 'Default',
    ])->save();
    $variation = ProductVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => '10.00',
        'currency_code' => 'USD',
      ],
    ]);
    $variation->save();

    $subscription = Subscription::create([
      'type' => 'product',
      'billing_schedule' => $billing_schedule,
      'uid' => 0,
      'payment_method' => $payment_method,
      'purchased_entity' => $variation,
      'amount' => new Price('2', 'USD'),
      'state' => 'active',
      'created' => 1507642328,
      'started' => 1507642328 + 10,
      'ended' => 1507642328 + 50,
    ]);
    $subscription
      ->save();

    $subscription = Subscription::load($subscription->id());
    $this->assertEquals('product', $subscription->bundle());
    $this->assertEquals($payment_method->id(), $subscription->getPaymentMethod()->id());
    $this->assertEquals($payment_method->id(), $subscription->getPaymentMethodId());
    $this->assertTrue($subscription->hasPurchasedEntity());
    $this->assertEquals($variation->id(), $subscription->getPurchasedEntity()->id());
    $this->assertEquals($variation->id(), $subscription->getPurchasedEntityId());
    $this->assertEquals(2, $subscription->getAmount()->getNumber());
    $this->assertEquals(1507642328, $subscription->getCreatedTime());
    $this->assertEquals(1507642328 + 10, $subscription->getStartTime());
    $this->assertEquals(1507642328 + 50, $subscription->getEndTime());

    // Modify some values.
    $subscription->setAmount(new Price('3', 'USD'));
    $subscription->setStartTime(12345);
    $subscription->setEndTime(123456);
    $subscription->save();

    $subscription = Subscription::load($subscription->id());
    $this->assertEquals(3, $subscription->getAmount()->getNumber());
    $this->assertEquals(12345, $subscription->getStartTime());
    $this->assertEquals(123456, $subscription->getEndTime());
  }
  

}
