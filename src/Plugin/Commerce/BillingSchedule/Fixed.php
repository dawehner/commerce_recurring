<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @CommerceBillingSchedule(
 *   id = "fixed",
 *   label = @Translation("Fixed interval"),
 * )
 */
class Fixed extends IntervalBase {

  /**
   * {@inheritdoc}
   */
  public function getFirstBillingCycle(DrupalDateTime $start_time) {
    $start_time = clone $start_time;

    switch ($this->configuration['unit']) {
      case 'hour':
        $start_time->setTime($start_time->format('G'), 0);
        break;
      case 'day':
        $start_time->modify('midnight');
        break;
      case 'week':
        $start_time->modify('monday');
        break;
      case 'month':
        $start_time->modify('first day of this month');
        break;
      case 'year':
        $start_time->modify('first day of january');
        break;
      default:
        throw new \Exception('You missed a case ...');
    }

    $end_date = clone $start_time;
    $end_date = $this->modifyTime($end_date, $this->configuration['number'], $this->configuration['unit']);

    // @todo Provide edge handling. Maybe the scheduling queue should actually
    //   handle that.
    return new BillingCycle(0, $start_time, $end_date);
  }

}
