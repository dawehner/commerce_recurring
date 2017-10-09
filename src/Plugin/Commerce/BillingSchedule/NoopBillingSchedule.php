<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;

/**
 * @BillingSchedule(id = "noop", label=@Translation("Default"))
 */
class NoopBillingSchedule extends BillingScheduleBase {

  /**
   * {@inheritdoc}
   */
  public function getBillingCycle(AccountInterface $account, DrupalDateTime $startTime) {
    return new BillingCycle('todo', new DrupalDateTime(), new DrupalDateTime());
  }

  /**
   * {@inheritdoc}
   */
  public function getNextBillingCycle(BillingCycle $cycle) {
    return new BillingCycle('todo', new DrupalDateTime(), new DrupalDateTime());
  }

  /**
   * {@inheritdoc}
   */
  public function renewCycle(BillingCycle $cycle) {
  }

  /**
   * {@inheritdoc}
   */
  public function closeCycle(BillingCycle $cycle) {
  }

}
