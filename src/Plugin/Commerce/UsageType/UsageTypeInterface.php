<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\UsageType;

use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Usage group plugin type.
 */
interface UsageTypeInterface {

  /**
   * Determines whether an order contains the necessary information for usage
   * groups of this type to function. The default usage types depend on the
   * recurring info field defined on the default recurring order bundle and
   * used by the default periodic recurring engine implementation, and this
   * function is used to validate that these usage types will behave properly
   * (or throw an exception) if attached to a subscription which belongs to a
   * recurring order type that lacks this information.
   *
   * Other implementations may not require this information or might require
   * other order fields (thus depending on a recurring order bundle provided
   * by a different recurring engine plugin) and these methods should be
   * implemented accordingly.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order to be validated.
   */
  public function validateOrder(OrderInterface $order);

  /**
   * Determines whether this usage group plugin should block a given property
   * of a subscription from being changed.
   *
   * @param string $property
   *   The property which is being changed.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The recurring order during which the change would fall if scheduling
   *   is not enforced.
   */
  public function enforceChangeScheduling($property, OrderInterface $order);

  /**
   * Returns a list of usage records for this usage group and a given recurring
   * order.
   *
   * @TODO: Should this be on the group itself, or are we spawning a plugin for
   * each group?
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order for which usage
   */
  public function usageHistory(OrderInterface $order);

  /**
   * Adds usage for this usage group and subscription and recurring order.
   * Because this function's parameters change with each implementation, we
   * declare the interface method with a single variadic parameter, allowing
   * each implementation to override it with its own list of more specific
   * parameters if desired.
   *
   * @param mixed ...$params
   *   The usage parameters.
   */
  public function addUsage(...$params);

  /**
   * Gets the current usage (normally an integer, but who knows) for this usage
   * group. This is a convenience method that would (in 1.x) look up the current
   * recurring order for a user and get the usage based on this, but this is
   * probably brittle and it isn't clear if we want to keep this around.
   *
   * @TODO: Follow up on this.
   */
  public function currentUsage();

  /**
   * Checks whether usage records are complete for a given recurring
   * order or whether the subscription needs to "wait" on remote
   * services that might record usage data into the system later.
   *
   * @param \Drupal\commerce_order\OrderInterface $order
   *   The order for which usage completion should be checked.
   */
  public function isComplete(OrderInterface $order);

  /**
   * Returns the charges for this group and a given recurring order.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface
   *   The order for which charges are being computed.
   *
   * @return \Drupal\commerce_recurring\Charge[]
   *   The computed list of charges.
   */
  public function getCharges(OrderInterface $order);

  /**
   * We need something to react to changes in the subscription plan.
   * In 1.x this was "onRevisionChange" but that probably doesn't make sense.
   *
   * @TODO: Figure out the parameters for this, if any.
   */
  public function onPlanChange();
}

