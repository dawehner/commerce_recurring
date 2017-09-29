<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\UsageType;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\commerce_recurring\Usage\UsageRecordStorageInterface;

/**
 * Usage type plugin base class.
 *
 * Implements logic which is likely to be shared between all implementations.
 */
abstract class UsageTypeBase implements UsageTypeInterface {

  /**
   * The usage record storage class.
   *
   * @var \Drupal\commerce_recurring\Usage\UsageRecordStorageInterface
   */
  protected $storage;

  /**
   * The group name.
   *
   * @var string
   */
  protected $groupName;

  /**
   * The group definition/info.
   *
   * @var array
   */
  protected $groupInfo;

  /**
   * The subscription entity which owns this instance of the usage group.
   *
   * @var \Drupal\commerce_recurring\Entity\SubscriptionInterface
   */
  protected $subscription;

  /**
   * Instantiate a new usage type plugin.
   */
  public function __construct(UsageRecordStorageInterface $storage, $groupName, $groupInfo, SubscriptionInterface $subscription) {
    $this->storage = $storage;
    $this->groupName = $groupName;
    $this->groupInfo = $groupInfo;
    $this->subscription = $subscription;
  }

  /**
   * Arbitrary getter for properties, including group info.
   *
   * @param string $property
   *   The property or group info key to get.
   */
  public function __get($property) {
    if (!empty($this->{$property})) {
      return $this->{$property};
    }
    elseif (!empty($this->groupInfo[$property])) {
      return $this->groupInfo[$property];
    }
  }

  /**
   * The default behavior is for usage groups to not enforce change scheduling.
   */
  public function enforceChangeScheduling($property, OrderInterface $order) {
    return FALSE;
  }
}

