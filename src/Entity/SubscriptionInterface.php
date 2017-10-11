<?php

namespace Drupal\commerce_recurring\Entity;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_payment\Entity\PaymentMethodInterface;
use Drupal\commerce_price\Price;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Defines the interface for payments.
 */
interface SubscriptionInterface extends ContentEntityInterface {

  /**
   * Gets the billing schedule.
   *
   * @return \Drupal\commerce_recurring\Entity\BillingScheduleInterface
   */
  public function getBillingSchedule();

  /**
   * Sets the billing schedule.
   *
   * @param \Drupal\commerce_recurring\Entity\BillingScheduleInterface $billing_schedule
   *   The billing schedule.
   *
   * @return $this
   */
  public function setBillingSchedule(BillingScheduleInterface $billing_schedule);

  /**
   * Gets the payment method.
   *
   * @return \Drupal\commerce_payment\Entity\PaymentMethodInterface|null
   *   The payment method entity, or null if unknown.
   */
  public function getPaymentMethod();

  /**
   * Sets the payment method
   *
   * @param \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method
   *
   * @return $this
   */
  public function setPaymentMethod(PaymentMethodInterface $payment_method);

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
   * Sets the purchased entity.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $purchased_entity
   *   The purchased entity.
   *
   * @return $this
   */
  public function setPurchasedEntity(PurchasableEntityInterface $purchased_entity);

  /**
   * Gets the purchased entity ID.
   *
   * @return int
   *   The purchased entity ID.
   */
  public function getPurchasedEntityId();

  /**
   * Gets the payment amount.
   *
   * @return \Drupal\commerce_price\Price|null
   *   The payment amount, or NULL.
   */
  public function getAmount();

  /**
   * Sets the payment amount.
   *
   * @param \Drupal\commerce_price\Price $amount
   *   The payment amount.
   *
   * @return $this
   */
  public function setAmount(Price $amount);

  /**
   * Gets the order state.
   *
   * @return \Drupal\state_machine\Plugin\Field\FieldType\StateItemInterface
   *   The order state.
   */
  public function getState();

  /**
   * Sets the customer user.
   *
   * @param \Drupal\user\UserInterface $account
   *   The customer user entity.
   *
   * @return $this
   */
  public function setCustomer(UserInterface $account);

  /**
   * Gets the customer user ID.
   *
   * @return int|null
   *   The customer user ID, or NULL in case the order is anonymous.
   */
  public function getCustomerId();

  /**
   * Sets the customer user ID.
   *
   * @param int $uid
   *   The customer user ID.
   *
   * @return $this
   */
  public function setCustomerId($uid);

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
