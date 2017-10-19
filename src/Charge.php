<?php

namespace Drupal\commerce_recurring;

use Drupal\commerce_price\Price;
use Drupal\Core\Datetime\DrupalDateTime;

class Charge {

  /**
   * The amount of the charge.
   *
   * @var \Drupal\commerce_price\Price
   */
  protected $amount;

  /**
   * The label.
   *
   * @var string
   */
  protected $label;

  /**
   * The start time.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $startTime;
  
  /**
   * The end time.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $endTime;

  /**
   * Charge constructor.
   *
   * @param \Drupal\commerce_price\Price $amount
   *   The amount of the charge.
   * @param string $label
   *   The label of the change.
   * @param \Drupal\Core\Datetime\DrupalDateTime $start_time
   *    The start time.
   * @param \Drupal\Core\Datetime\DrupalDateTime $end_time
   *   The end time.
   */
  public function __construct(Price $amount, $label, DrupalDateTime $start_time, DrupalDateTime $end_time) {
    $this->amount = $amount;
    $this->label = $label;
    $this->startTime = $start_time;
    $this->endTime = $end_time;
  }

  /**
   * @return \Drupal\commerce_price\Price
   */
  public function getAmount() {
    return $this->amount;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getStartTime() {
    return $this->startTime;
  }

  /**
   * @return \Drupal\Core\Datetime\DrupalDateTime
   */
  public function getEndTime() {
    return $this->endTime;
  }

}
