<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Plugin for billing cycle types.
 *
 * The plugin is responsible for calculating billing cycles, aka. when is the
 * next time someone should pay again.
 */
interface BillingScheduleInterface extends ConfigurablePluginInterface, PluginFormInterface, PluginInspectionInterface {

  /**
   * @param \Drupal\Core\Datetime\DrupalDateTime $start_time
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getFirstBillingCycle(DrupalDateTime $start_time);

  /**
   * @param \Drupal\commerce_recurring\BillingCycle $cycle
   *
   * @return \Drupal\commerce_recurring\BillingCycle
   */
  public function getNextBillingCycle(BillingCycle $cycle);

}
