<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_recurring\BillingCycle;
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
   *
   * @return \Drupal\commerce_recurring\Charge[]
   */
  public function collectCharges(BillingCycle $billing_cycle);

}


