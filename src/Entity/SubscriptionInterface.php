<?php

namespace Drupal\commerce_recurring\Entity;

use Drupal\commerce_price\Price;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for payments.
 */
interface SubscriptionInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Gets the billing schedule.
   *
   * @return \Drupal\commerce_recurring\Entity\BillingScheduleInterface
   */
  public function getBillingSchedule();

  /**
   * Gets the payment method.
   *
   * @return \Drupal\commerce_payment\Entity\PaymentMethodInterface|null
   *   The payment method entity, or null if unknown.
   */
  public function getPaymentMethod();

  /**
   * Gets the payment method ID.
   *
   * @return int|null
   *   The payment method ID, or null if unknown.
   */
  public function getPaymentMethodId();

  /**
   * Gets whether the order item has a purchased entity.
   *
   * @return bool
   *   TRUE if the order item has a purchased entity, FALSE otherwise.
   */
  public function hasPurchasedEntity();

  /**
   * Gets the purchased entity.
   *
   * @return \Drupal\commerce\PurchasableEntityInterface|null
   *   The purchased entity, or NULL.
   */
  public function getPurchasedEntity();

  /**
   * Gets the purchased entity ID.
   *
   * @return int
   *   The purchased entity ID.
   */
  public function getPurchasedEntityId();

  /**
   * Gets the subscription amount.
   *
   * @return int
   *   The subscription amount.
   */
  public function getAmount();

  /**
   * Sets the subscription amount.
   *
   * @param int $amount
   *   The subscription amount.
   *
   * @return $this
   */
  public function setAmount($amount);

  /**
   * Gets the order state.
   *
   * @return \Drupal\state_machine\Plugin\Field\FieldType\StateItemInterface
   *   The order state.
   */
  public function getState();

  /**
   * Gets the created timestamp.
   *
   * @return int
   *   The created timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the created timestamp.
   *
   * @param int $timestamp
   *   The created timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the timestamp the subscription starts.
   *
   * @return int
   *   The start timestamp.
   */
  public function getStartTime();

  /**
   * Sets the timestamp the subscription starts.
   *
   * @param int $timestamp
   *   The start timestamp.
   *
   * @return $this
   */
  public function setStartTime($timestamp);

  /**
   * Gets the timestamp the subscription end, 0 if there none.
   *
   * @return int
   *   The end timestamp.
   */
  public function getEndTime();

  /**
   * Sets the timestamp the subscription ends, 0 if there is none.
   *
   * @param int $timestamp
   *   The end timestamp.
   *
   * @return $this
   */
  public function setEndTime($timestamp);

}
