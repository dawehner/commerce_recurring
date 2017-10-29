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
final class BillingCycle {

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
  protected $startDate;

  /**
   * The time the billing cycle ends.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $endDate;

  /**
   * BillingCycle constructor.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $startDate
   *   The start time.
   * @param \Drupal\Core\Datetime\DrupalDateTime $endDate
   *   The end time.
   */
  public function __construct($index, DrupalDateTime $startDate, DrupalDateTime $endDate) {
    $this->index = $index;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  /**
   * @return int
   */
  public function getIndex() {
    return $this->index;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getStartDateTime() {
    return $this->startDate;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getEndDateTime() {
    return $this->endDate;
  }

}
