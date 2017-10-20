<?php

namespace Drupal\commerce_recurring\SubscriptionChange;

class SubscriptionChange {

  /**
   * The subscription which should be changed.
   *
   * @var int
   */
  protected $subscriptionId;

  /**
   * The field to be the changed.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The value of the property to be changed.
   *
   * @var mixed
   */
  protected $value;

  /**
   * The timestamp the scheduled change got created.
   *
   * @var int
   */
  protected $created;

  /**
   * ScheduledChange constructor.
   * @param int $subscription_id
   * @param string $field_name
   * @param mixed $value
   * @param int $created
   */
  public function __construct($subscription_id, $field_name, $value, $created = NULL) {
    $this->subscriptionId = $subscription_id;
    // @todo We could validate the fields being supported here.
    $this->fieldName = $field_name;
    $this->value = $value;
    $this->created = $created;
  }

  /**
   * @return int
   */
  public function getSubscriptionId() {
    return $this->subscriptionId;
  }

  /**
   * @return string
   */
  public function getFieldName() {
    return $this->fieldName;
  }

  /**
   * @return mixed
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @return int
   */
  public function getCreated() {
    return $this->created;
  }

}
