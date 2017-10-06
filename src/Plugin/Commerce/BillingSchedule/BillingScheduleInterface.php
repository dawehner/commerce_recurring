<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_recurring\Entity\RecurringCycleTypeInterface;
use Drupal\commerce_recurring\Entity\RecurringCycleInterface;

/**
 * Entity bundle plugin for billing cycle types.
 */
interface BillingScheduleInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {
}

