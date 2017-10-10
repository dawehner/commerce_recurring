<?php

namespace Drupal\Tests\commerce_recurring\Functional;

use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\commerce_payment\Entity\PaymentMethod;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the subscription UI.
 */
class SubscriptionTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring', 'commerce_recurring_test', 'block', 'commerce_product', 'commerce_payment_example'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

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
    $this->billingSchedule = $billing_schedule;

    /** @var \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method */
    $payment_gateway = PaymentGateway::create([
      'id' => 'example',
      'label' => 'Example',
      'plugin' => 'example_onsite',
    ]);
    $payment_gateway->save();
    $this->paymentGateway = $payment_gateway;

    /** @var \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method */
    $payment_method = PaymentMethod::create([
      'type' => 'credit_card',
      'payment_gateway' => $payment_gateway,
      'card_type' => 'visa',
    ]);
    $payment_method->save();
    $this->paymentMethod = $payment_method;

//    ProductVariationType::create([
//      'id' => 'default',
//      'label' => 'Default',
//    ])->save();
    ProductType::create([
      'id' => 'test_product'
    ])->save();
    $product = Product::create([
      'title' => 'Test product',
      'type' => 'test_product',
      'variations' => [],
    ]);
    $product->save();
    $variation = ProductVariation::create([
      'type' => 'default',
      'title' => 'Test title',
      'product_id' => $product,
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => '10.00',
        'currency_code' => 'USD',
      ],
    ]);
    $variation->save();
    $this->variation = $variation;
  }

  public function testCrudUiTest() {
    $admin_user = $this->drupalCreateUser(['administer subscriptions', 'administer commerce_payment_method']);
    $this->drupalLogin($admin_user);
    $this->placeBlock('local_actions_block');

    // 1. Create an entity
    $this->drupalGet('admin/commerce/subscriptions');
    $this->assertSession()->statusCodeEquals(200);

    $this->clickLink('Add subscription');
    $this->clickLink('Test plugin');
    $this->submitForm([
//      'test_plugin[0][value]' => 'test value',
      'billing_schedule[0][target_id]' => 'a (' . $this->billingSchedule->id() . ')',
      'payment_method[0][target_id]' => 'a (' . $this->paymentMethod->id() . ')',
      'purchased_entity[0][target_id]' => 'a (' . $this->variation->id() . ')',
      'amount[0][value]' => 4,
      'started[0][value][date]' => '2017-10-28',
      'started[0][value][time]' => '00:00:01',
      'ended[0][value][date]' => '2017-10-28',
      'ended[0][value][time]' => '00:00:01',
    ], 'Save');
    $this->assertSession()->addressEquals('admin/commerce/subscriptions');
    $this->assertSession()->pageTextContains('A subscription been successfully saved.');

    // 2. Ensure the entity is listed
    $table = $this->xpath('//table/tbody/tr');
    $this->assertCount(1, $table);

    // 3. Edit the entity
    $this->clickLink('Edit');
    $this->assertSession()->fieldValueEquals('amount[0][value]', 4);
    $this->submitForm([
      'amount[0][value]' => 5,
    ], 'Save');
    $this->assertSession()->addressEquals('admin/commerce/subscriptions');
    $this->clickLink('Edit');
    $this->assertSession()->fieldValueEquals('amount[0][value]', 5);
    $this->submitForm([], 'Save');

    // 4. Delete the entity
    $this->clickLink('Delete');
    $this->submitForm([], 'Delete');

    $this->assertSession()->pageTextContains('There is no Subscription yet.');
  }

}
