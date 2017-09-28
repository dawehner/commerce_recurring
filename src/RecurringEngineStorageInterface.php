<?php

namespace Drupal\commerce_recurring;

use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;

/**
 * Defines the interface for payment gateway storage.
 */
interface RecurringEngineStorageInterface extends ConfigEntityStorageInterface {

  /**
   * Selects a recurring engine for a subscription. Normally this is defined by
   * plugin configuration via a trait on the subscription's plan, product,
   * license, or other purchasable entity, but the default entity storage
   * allows this to be altered by an event.
   *
   * @TODO: Clean this description up once we figure out exactly how a default
   * recurring engine is selected.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription;
   *   The subscription requesting a recurring engine.
   *
   * @return \Drupal\commerce_recurring\Entity\RecurringEngineInterface
   *   The selected recurring engine.
   */
  public function selectRecurringEngine(SubscriptionInterface $subscription);

  // @TODO: As noted in the class file, we don't know what we need here
  // so I'm keeping the payment gateway scaffolding around as a reminder
  // to look at this later. Primarily I think we need this to allow a
  // "whatever" (probably a license or product - unclear?) to fire an alter
  // event when requesting its assigned recurring engine.
  //
  // /**
  //  * Loads the default payment gateway for the given user.
  //  *
  //  * Used primarily when adding payment methods from the user pages.
  //  * Thus, only payment gateways which support storing payment methods
  //  * are considered.
  //  *
  //  * @param \Drupal\user\UserInterface $account
  //  *   The user account.
  //  *
  //  * @return \Drupal\commerce_payment\Entity\PaymentGatewayInterface
  //  *   The payment gateway.
  //  */
  // public function loadForUser(UserInterface $account);

  // /**
  //  * Loads all eligible payment gateways for the given order.
  //  *
  //  * @param \Drupal\commerce_order\Entity\OrderInterface $order
  //  *   The order.
  //  *
  //  * @return \Drupal\commerce_payment\Entity\PaymentGatewayInterface[]
  //  *   The payment gateways.
  //  */
  // public function loadMultipleForOrder(OrderInterface $order);
}

