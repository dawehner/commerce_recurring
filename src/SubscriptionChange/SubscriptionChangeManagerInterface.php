<?php

namespace Drupal\commerce_recurring\SubscriptionChange;

use Drupal\commerce_recurring\Entity\SubscriptionInterface;

interface SubscriptionChangeManagerInterface {

  /**
   * Adds subscription changes.
   *
   * @param \Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange $subscription_change
   *   The subscription change to be added.
   */
  public function addScheduledChange(SubscriptionChange $subscription_change);

  /**
   * Load changes which should be applied to subscriptions.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription
   *   The subscription to load changes for.
   *
   * @return \Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange[]
   */
  public function getChangesPerSubscription(SubscriptionInterface $subscription);

  /**
   * Applies a list of subscription changes to a given subscription.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription
   *   The subscription that should be changed.
   * @param \Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange[] $subscription_changes
   *   The changes which will be applied.
   *
   * @return \Drupal\commerce_recurring\Entity\SubscriptionInterface
   *   The changed subscription.
   */
  public function applyChanges(SubscriptionInterface $subscription, array $subscription_changes);

}
