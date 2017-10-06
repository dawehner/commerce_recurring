<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\commerce\BundlePluginInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Entity bundle plugin for billing cycle types.
 */
interface BillingScheduleInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   * @param \DateTime $startTime
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getBillingCycle(AccountInterface $account, \DateTime $startTime);

  /**
   * @param \Drupal\commerce_recurring\BillingCycle $cycle
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getNextBillingCycle(BillingCycle $cycle);

  public function renewCycle(BillingCycle $cycle);

  public function closeCycle(BillingCycle $cycle);

}

