<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_recurring\Charge;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Defines the subscription base class.
 */
abstract class SubscriptionTypeBase extends PluginBase implements SubscriptionTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function createRecurringOrder(SubscriptionInterface $subscription) {
    /** @var \Drupal\commerce_order\OrderItemStorageInterface $order_item_storage */
    $order_item_storage = \Drupal::entityTypeManager()
      ->getStorage('commerce_order_item');

    $start_time = DrupalDateTime::createFromTimestamp($subscription->getStartTime());
    $initial_billing_cycle = $subscription->getBillingSchedule()
      ->getPlugin()
      ->getFirstBillingCycle($start_time);
    $initial_charges = $subscription->getType()->collectCharges($initial_billing_cycle, $subscription);

    // Create a recurring order.
    $order = Order::create([
      'type' => 'recurring',
      'uid' => $subscription->getCustomer(),
      // @todo Is this the right store?
      'store_id' => \Drupal::service('commerce_store.current_store')->getStore(),
      'started' => $initial_billing_cycle->getStartDateTime()->format('U'),
      'ended' => $initial_billing_cycle->getEndDateTime()->format('U'),
    ]);

    foreach ($initial_charges as $charge) {
      // Create the initial order item.
      // @todo Take into account prepaid vs. postpaid
      $order_item = $order_item_storage->createFromPurchasableEntity($subscription, [
        'type' => 'recurring',
        'billing_schedule' => $subscription->getBillingSchedule(),
        'quantity' => 1,
        'unit_price' => $charge->getAmount(),
      ]);

      $order_item->save();
      $order->addItem($order_item);
    }

    $order->save();
    return $order;
  }

  /**
   * {@inheritdoc}
   */
  public function refreshRecurringOrder(SubscriptionInterface $subscription) {
    // Refresh an existing recurring order.

    // Try to find an order item which has this subscription attached.
    /** @var \Drupal\commerce_order\OrderItemStorageInterface $order_item_storage */
    $order_item_storage = \Drupal::entityTypeManager()
      ->getStorage('commerce_order_item');
    $result = $order_item_storage->getQuery()
      ->condition('type', 'recurring')
      ->condition('purchased_entity', $subscription->id())
      // Find the latest order item
      ->sort('order_item_id', 'DESC')
      ->pager(1)
      ->execute();
    if ($result && $order_item = OrderItem::load(reset($result))) {
      // @todo Implement the current refreshing logic ...
    }
  }

}

