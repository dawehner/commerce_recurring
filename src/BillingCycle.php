<?php

namespace Drupal\commerce_recurring;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Represents a single billing cycle.
 *
 * The billing schedule plugins are responsible for calculating the next
 * billing cycles.
 *
 * @see \Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\BillingScheduleInterface
 */
class BillingCycle {

  /**
   * A human readable label for the billing cycle.
   *
   * @var string
   */
  protected $label;

  /**
   * An increasing number for the billing cycle.
   *
   * @var int
   */
  protected $index;

  /**
   * The time the billing cycle starts.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $startDateTime;

  /**
   * The time the billing cycle ends.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $endDateTime;

  /**
   * BillingCycle constructor.
   *
   * @param string $label
   *   The label.
   * @param \Drupal\Core\Datetime\DrupalDateTime $startDateTime
   *   The start time.
   * @param \Drupal\Core\Datetime\DrupalDateTime $endDateTime
   *   The end time.
   */
  public function __construct($label, $index, DrupalDateTime $startDateTime, DrupalDateTime $endDateTime) {
    $this->label = $label;
    $this->index = $index;
    $this->startDateTime = $startDateTime;
    $this->endDateTime = $endDateTime;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @param string $label
   *
   * @return $this
   */
  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * @return int
   */
  public function getIndex() {
    return $this->index;
  }

  /**
   * @param int $index
   *
   * @return $this
   */
  public function setIndex($index) {
    $this->index = $index;
    return $this;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getStartDateTime() {
    return $this->startDateTime;
  }

  /**
   * @param \Drupal\Core\Datetime\DrupalDateTime $startDateTime
   *
   * @return $this
   */
  public function setStartDateTime($startDateTime) {
    $this->startDateTime = $startDateTime;
    return $this;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getEndDateTime() {
    return $this->endDateTime;
  }

  /**
   * @param \Drupal\Core\Datetime\DrupalDateTime $endDateTime
   *
   * @return $this
   */
  public function setEndDateTime($endDateTime) {
    $this->endDateTime = $endDateTime;
    return $this;
  }

}
