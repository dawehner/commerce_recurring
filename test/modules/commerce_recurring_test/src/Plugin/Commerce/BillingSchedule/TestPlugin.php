<?php

namespace Drupal\commerce_recurring_test\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\BillingScheduleBase;
use Drupal\Core\Session\AccountInterface;

/**
 * @BillingSchedule(
 *   id = "test_plugin",
 *   label = "Test label"
 * )
 */
class TestPlugin extends BillingScheduleBase {

  /**
   * {@inheritdoc}
   */
  public function getBillingCycle(AccountInterface $account, \DateTime $startTime) {
    return new BillingCycle('My first billing cycle', $startTime, $startTime->add(new \DateInterval()))
  }

  public function getNextBillingCycle(BillingCycle $cycle) {
    // TODO: Implement getNextBillingCycle() method.
  }

  public function renewCycle(BillingCycle $cycle) {
    // TODO: Implement renewCycle() method.
  }

  public function closeCycle(BillingCycle $cycle) {
    // TODO: Implement closeCycle() method.
  }


}
