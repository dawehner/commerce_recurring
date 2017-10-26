<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_order\Entity\OrderItemType;
use Drupal\commerce_order\Entity\OrderType;
use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\commerce_payment\Entity\PaymentMethod;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\field\Entity\FieldConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

class CommerceRecurringKernelTestBase extends CommerceKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'action',
    'advancedqueue',
    'address',
    'commerce',
    'commerce_order',
    'commerce_payment',
    'commerce_payment_example',
    'commerce_price',
    'commerce_product',
    'commerce_recurring',
    'commerce_recurring_test',
    'commerce_store',
    'entity',
    'entity_reference_revisions',
    'field',
    'inline_entity_form',
    'options',
    'path',
    'profile',
    'state_machine',
    'system',
    'text',
    'user',
    'views',
  ];

  /**
   * @var \Drupal\commerce_recurring\Entity\BillingScheduleInterface
   */
  protected $billingSchedule;

  /**
   * @var \Drupal\commerce_payment\Entity\PaymentGatewayInterface
   */
  protected $paymentGateway;

  /**
   * @var \Drupal\commerce_payment\Entity\PaymentMethodInterface
   */
  protected $paymentMethod;

  /**
   * @var \Drupal\commerce_product\Entity\ProductVariationInterface
   */
  protected $variation;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_payment');
    $this->installEntitySchema('commerce_payment_method');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_subscription');
    $this->installEntitySchema('user');
    $this->installSchema('advancedqueue', 'advancedqueue');
    $this->installConfig('entity');
    $this->installConfig('commerce_product');
    $this->installConfig('commerce_order');
    $this->installConfig('commerce_recurring');

    $this->billingSchedule = $billing_schedule = BillingSchedule::create([
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
    $this->paymentGateway = $payment_gateway = PaymentGateway::create([
      'id' => 'example',
      'label' => 'Example',
      'plugin' => 'example_onsite',
    ]);
    $payment_gateway->save();

    /** @var \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method */
    $this->paymentMethod = $payment_method = PaymentMethod::create([
      'type' => 'credit_card',
      'payment_gateway' => $payment_gateway,
    ]);
    $payment_method->save();

    ProductVariationType::create([
      'id' => 'with_subscriptions',
      'label' => 'Default',
      'traits' => ['purchasable_entity_subscription'],
    ])->save();

    $trait_manager = \Drupal::service('plugin.manager.commerce_entity_trait');
    $trait = $trait_manager->createInstance('purchasable_entity_subscription');
    $trait_manager->installTrait($trait, 'commerce_product_variation', 'with_subscriptions');

    $this->variation = $variation = ProductVariation::create([
      'type' => 'with_subscriptions',
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => '10.00',
        'currency_code' => 'USD',
      ],
      'billing_schedule' => $billing_schedule,
      'subscription_type' => ['target_plugin_id' => 'license'],
    ]);
    $variation->save();

    ProductType::create([
      'id' => 'with_subscriptions',
      'variationType' => $variation->id(),
    ])->save();

    FieldConfig::create([
      'entity_type'=> 'commerce_product',
      'bundle' => 'with_subscriptions',
      'field_name' => 'variations',
    ])->save();

    $product = Product::create([
      'title' => 'Product with subscriptions',
      'type' => 'with_subscriptions',
    ]);
    $product->addVariation($variation);
    $product->save();

    OrderType::create([
      'id' => 'with_subscriptions',
      'workflow'=> 'order_default',
    ])->save();

    OrderItemType::create([
      'id' => 'with_subscriptions',
      'purchasableEntityType' => 'commerce_product_variation',
      'orderType' => 'with_subscriptions',
    ])->save();

    FieldConfig::create([
      'entity_type' => 'commerce_order',
      'field_name' => 'order_items',
      'bundle' => 'with_subscriptions',
    ])->save();
  }
}
