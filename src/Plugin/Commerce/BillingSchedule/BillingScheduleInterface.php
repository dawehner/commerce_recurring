<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Entity bundle plugin for billing cycle types.
 */
interface BillingScheduleInterface extends ConfigurablePluginInterface, PluginFormInterface, PluginInspectionInterface {

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   * @param \Drupal\Core\Datetime\DrupalDateTime $startTime
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getFirstBillingCycle(AccountInterface $account, DrupalDateTime $startTime);

  /**
   * @param \Drupal\commerce_recurring\BillingCycle $cycle
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getNextBillingCycle(BillingCycle $cycle);

  public function renewCycle(BillingCycle $cycle);

  public function closeCycle(BillingCycle $cycle);

}

