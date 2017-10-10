<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @CommerceBillingSchedule(
 *   id = "rolling",
 *   label = @Translation("Rolling interval"),
 * )
 */
class Rolling extends IntervalBase {

  /**
   * {@inheritdoc}
   */
  public function getFirstBillingCycle(DrupalDateTime $start_time) {
    $start_time = clone $start_time;

    $end_date = clone $start_time;
    $end_date = $this->modifyTime($end_date, $this->configuration['number'], $this->configuration['unit']);

    return new BillingCycle(0, $start_time, $end_date);
  }

}
