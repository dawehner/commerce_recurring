<?php


namespace Drupal\commerce_recurring\Entity;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;

/**
 * Defines the interface for billing schedules configuration entities.
 *
 * Stores configuration for billing schedules engine plugins.
 */
interface BillingScheduleInterface extends ConfigEntityInterface, EntityWithPluginCollectionInterface {

  /**
   * Gets the customer facing label.
   *
   * @return string
   */
  public function getDisplayLabel();

  /**
   * Gets the billing schedule engine plugin.
   *
   * @return \Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\BillingScheduleInterface
   *   The billing schedule plugin.
   */
  public function getPlugin();

  /**
   * Gets the billing schedule plugin ID.
   *
   * @return string
   *   The billing schedule plugin ID.
   */
  public function getPluginId();

  /**
   * Sets the billing schedule plugin ID.
   *
   * @param string $plugin_id
   *   The billing schedule plugin ID.
   *
   * @return $this
   */
  public function setPluginId($plugin_id);

  /**
   * Gets the billing schedule plugin configuration.
   *
   * @return string
   *   The billing schedule plugin configuration.
   */
  public function getPluginConfiguration();

  /**
   * Sets the billing schedule plugin configuration.
   *
   * @param array $configuration
   *   The recurring engine plugin configuration.
   *
   * @return $this
   */
  public function setPluginConfiguration(array $configuration);

}

