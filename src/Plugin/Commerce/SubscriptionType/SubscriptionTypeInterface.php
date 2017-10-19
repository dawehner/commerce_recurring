<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Defines the interface for subscription types.
 *
 * @todo What needs to be done on subscription types ...
 */
interface SubscriptionTypeInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns charges needed for a given billing cycle.
   *
   * @param \Drupal\commerce_recurring\BillingCycle $billing_cycle
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription
   *
   * @return \Drupal\commerce_recurring\Charge[]
   */
  public function collectCharges(BillingCycle $billing_cycle, SubscriptionInterface $subscription);

  /**
   * Create orders and order items
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription
   * @return \Drupal\commerce_order\Entity\OrderInterface
   */
  public function createRecurringOrder(SubscriptionInterface $subscription);

  public function refreshRecurringOrder(SubscriptionInterface $subscription, OrderInterface $previous_recurring_order);

}


