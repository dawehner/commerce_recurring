<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\RecurringEngine;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_recurring\Entity\RecurringCycleTypeInterface;
use Drupal\commerce_recurring\Entity\RecurringCycleInterface;

/**
 * Entity bundle plugin for billing cycle types.
 */
interface RecurringEngineInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {
  // @TODO: Add stuff that all bundle plugin interfaces need here, if any.

  /**
   * @param Account $account
   * @param DateTime $startTime
   * @return RecurringCycle $cycle
   */
  getRecurringCycle(Account $account, DateTime(?) $startTime)

  /**
   * @param RecurringCycle $cycle
   * @return RecurringCycle $newCycle
   */
  getNextRecurringCycle(RecurringCycleInterface $cycle)

  /**
   * @param RecurringCycle $cycle
   * @return ??? $status
   * Renew the cycle. Base implementation mimicks the 1.x version:
   *   - Change the cycle workflow (?) to renewed (and bail if renewal has
   *   already taken place? Unclear.)
   *   - Get all subscriptions from the order attached to the cycle
   *   - Run all scheduled changes on each subscription, if any
   *   - Renew all subscriptions
   *   - For each subscription, get the billing cycle type and next cycle
   *   - For each cycle type + license list, create a new recurring order
   * Non-standard implementations of this are possible and at-your-own-risk.
   */
  renewCycle(RecurringCycleInterface $cycle);

  /**
   * @param RecurringCycle $cycle
   * @return ??? $status
   * Close the cycle. Base implementation mimicks the 1.x version:
   *   - Change the cycle workflow (?) to closed (and bail if it is already
   *   closed? Unclear.)
   *   - Get all the subscriptions from the order.
   *   - For each subscription, check if it can be charged
   *   - Normally this is about usage groups but other implementations are
   *   possible
   *   - Move the order workflow to payment pending (?) if possible
   *   - Do any requested cleanup from ... the subscription/usage groups/billing
   *   cycle type? Gotta think about this.
   *   - Otherwise change order workflow to completion_pending? (Formerly
   *   usage_pending...)
   */
  closeCycle(RecurringCycleInterface $cycle);

  /**
   * @param RecurringOrder $order
   * @return ??? $status
   * Refreshes an order.
   *
   * @TODO: Assuming we stick with this existing on the recurring engine
   * plugin, we'll want our custom order refresher to phone home to this.
   *
   */
  refreshOrder(Order $order);

  /**
   * @param Order $previousOrder
   * @param RecurringCycle $cycle
   * @param Subscription[] $subscriptions
   * @return RecurringOrder $newOrder
   * Generate a recurring order for a set of subscriptions.
   *   - If an order already exists for the billing cycle, it will be used
   *   - Otherwise a new order is generated
   *   - A defined set of values (especially customer profiles) are copied to
   *   the new order
   *   - Add the requested subscriptions to the attachment subscription
   *   reference array on the order
   */
  createRecurringOrder($previousOrder, $cycle, $subscriptions);

  /**
   * @TODO: Figure out how the billing engine needs to be involved in order
   * item creation and pricing, if at all. I feel like for the periodic plugin
   * type to work we should not be hard-coding any of its time-based assumptions
   * but I'm not sure of the code layout that will give us what we want here.
   *
   * Stay tuned.
   */
}

