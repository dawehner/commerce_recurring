<?php

namespace Drupal\commerce_recurring;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;

/**
 * Provides the default database storage backend for usage records.
 */
class UsageRecordDatabaseStorage implements UsageRecordStorageInterface {
  /**
   * The database connection in use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs the usage record storage.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection for usage record storage.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Fetch all records which pertain to a given group, subscription, and
   * recurring order.
   *
   * @param string $group_name
   *   The group name. Required.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface
   *   The subscription. Optional.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface
   *   The recurring order. Optional.
   */
  public function fetchRecords($group_name, SubscriptionInterface $subscription = NULL, OrderInterface $order = NULL) {

  }

  /**
   * Run multiple storage operations while enforcing consistency.
   *
   * @param array $operations
   *   A list of operations, each consisting of a 2-element array composed of:
   *
   *   1. A method name
   *   2. A list of arguments
   *
   * @return bool
   *   Whether or not the operations succeeded.
   */
  public function doMultiple($operations) {
    // @TODO: Open a transaction.
    //
    try {
      // Run each operation.
      foreach ($operations as $op) {
        // The second element of the operation should be a list of arguments.
        list($method, $args) = $op;
        $this->$method(...$args);
      }

      // Hopefully we're done here.
      $transaction->commit();

      return TRUE;
    }
    catch (DatabaseException $e) {
      // Something went wrong. Sad.
      $transaction->rollback();

      return FALSE;
    }
  }

  /**
   * Insert a usage record.
   *
   * @param mixed $record
   *   The usage record to be inserted.
   */

}

