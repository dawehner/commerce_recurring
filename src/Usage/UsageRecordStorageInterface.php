<?php

namespace Drupal\commerce_recurring\Usage;

use Drupal\commerce_recurring\Usage\UsageRecordInterface;

/**
 * Storage interface for usage records.
 */
interface UsageRecordStorageInterface {

  /**
   * Fetch all records which pertain to a given group, subscription, and
   * recurring order.
   *
   * @param string $group_name
   *   The group name.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface
   *   The subscription.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface
   *   The recurring order.
   */
  public function fetchOrderRecords($group_name, $subscription, $order);

  /**
   * Fetch all records which pertain to a given group and subscription.
   *
   * @param string $group_name
   *   The group name.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface
   *   The subscription.
   */
  public function fetchSubscriptionRecords($group_name, $subscription);

  /**
   * Create a usage record.
   *
   * @param \Drupal\commerce_recurring\Usage\UsageRecordInterface
   *   The usage record to be created.
   *
   * @return void
   */
  public function createRecord(UsageRecordInterface $record);

  /**
   * Update a usage record.
   *
   * @param \Drupal\commerce_recurring\Usage\UsageRecordInterface
   *   The usage record to be modified.
   *
   * @return void
   */
  public function modifyRecord(UsageRecordInterface $record);

  /**
   * Perform multiple usage operations in a consistent way, failing if one or
   * more of the requested operations does not succeed.
   *
   * @param array $operations
   *   A list of operations, each consisting of a 2-element array composed of:
   *
   *   1. A method name
   *   2. A list of arguments
   *
   * @return void
   */
  public function doMultiple($operations);
}

