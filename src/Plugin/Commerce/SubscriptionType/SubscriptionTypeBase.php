<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_recurring\BillingCycle;
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
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {}

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
   *
   * @todo Should we inform the billing schedule plugin for close and renewing?
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
      'started' => $initial_billing_cycle->getStartDate()->format('U'),
      'ended' => $initial_billing_cycle->getEndDate()->format('U'),
    ]);

    $start_time = DrupalDateTime::createFromTimestamp($subscription->getStartTime());
    $initial_billing_cycle = $subscription->getBillingSchedule()
      ->getPlugin()
      ->getFirstBillingCycle($start_time);

    $initial_charges = $subscription->getType()->collectCharges($initial_billing_cycle, $subscription);

    foreach ($initial_charges as $charge) {
      // Create the initial order item.
      // @todo Take into account prepaid vs. postpaid
      $order_item = $order_item_storage->createFromPurchasableEntity($subscription, [
        'type' => 'recurring',
        'title' => $charge->getLabel(),
        'billing_schedule' => $subscription->getBillingSchedule(),
        'quantity' => 1,
        'unit_price' => $charge->getAmount(),
        'started' => $charge->getStartTime()->format('U'),
        'ended' => $charge->getEndTime()->format('U'),
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
  public function renewRecurringOrder(SubscriptionInterface $subscription, OrderInterface $previous_recurring_order) {
    /** @var \Drupal\commerce_order\OrderItemStorageInterface $order_item_storage */
    $order_item_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_item');

    $current_billing_cycle = new BillingCycle(DrupalDateTime::createFromTimestamp($previous_recurring_order->get('started')->value), DrupalDateTime::createFromTimestamp($previous_recurring_order->get('ended')->value));
    $next_billing_cycle = $subscription->getBillingSchedule()->getPlugin()->getNextBillingCycle($current_billing_cycle);

    // Create the order for the next billing cycles.
    // @todo Take into account the schedules changes. In case the next cycle
    //   is changing, we need to switch now, so we don't end up with some lack
    //   of consistency.
    $next_order = Order::create([
      'type' => 'recurring',
      'uid' => $subscription->getCustomerId(),
      'store_id' => $previous_recurring_order->getStore(),
      'started' => $next_billing_cycle->getStartDate()->format('U'),
      'ended' => $next_billing_cycle->getEndDate()->format('U'),
    ]);

    $charges = $this->collectCharges($next_billing_cycle, $subscription);
    foreach ($charges as $charge) {
      $order_item = $order_item_storage->createFromPurchasableEntity($subscription, [
        'type' => 'recurring',
        'billing_schedule' => $subscription->getBillingSchedule(),
        'quantity' => 1,
        'unit_price' => $charge->getAmount(),
      ]);
      $order_item->save();
      $next_order->addItem($order_item);
    }
    $next_order->save();
    return $next_order;
  }

}
