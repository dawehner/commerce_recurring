<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Defines the interface for subscription types.
 */
interface SubscriptionTypeInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Gets the subscription type label.
   *
   * @return string
   *   The subscription type label.
   */
  public function getLabel();

  /**
   * Build a label for the given subscription type.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $license
   *
   * @return string
   *   The label.
   */
  public function buildLabel(SubscriptionInterface $subscription);

  /**
   * Gets the workflow ID this this subscription type should use.
   *
   * @return string
   *   The ID of the workflow used for this subscription type.
   */
  public function getWorkflowId();

  /**
   * Generate the charges for this subscription and a given recurring cycle.
   *   Default implementation here varies a lot:
   *   Does the subscription represent:
   *     a. A single product or bundle?
   *     b. An entire cart / repeated order of some kind?
   *     c. A license which has recurring billing configured?
   *     d. Any one of these things, plus usage groups?

   *   The answer to this question is one of the key parts of the
   *   subscription type plugin and determines the implementation.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order for which this subscription should compute charges.
   *
   * @TODO: Define here what a charge object is and how to return one.
   * @return \Drupal\commerce_recurring\Charge[]
   *   The collected charges.
   */
  public function collectCharges(OrderInterface $order);

  /**
   * Get the default recurring engine config entity for this subscription.
   * Because each subscription refers to its subscribable entity differently
   * (via bundle plugin fields) we use this method to get the desired recurring
   * engine, which is normally configured at the product level and needs to be
   * reached differently depending on whether this subscription is for an order,
   * product, product bundle, or license.
   *
   * @return \Drupal\commerce_recurring\Entity\RecurringEngineInterface
   */
  public function getDefaultRecurringEngine();

  /**
   * Check whether plan changes can be made to this subscription during
   * the middle of a recurring cycle.
   */
  public function enforceChangeScheduling(OrderInterface $order);
}


