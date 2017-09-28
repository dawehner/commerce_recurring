<?php


namespace Drupal\commerce_recurring\Entity;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;

/**
 * Defines the interface for recurring engine configuration entities.
 *
 * Stores configuration for recurring engine plugins.
 */
interface RecurringEngineInterface extends ConfigEntityInterface, EntityWithPluginCollectionInterface {

  /**
   * Gets the recurring engine weight.
   *
   * @return string
   *   The recurring engine weight.
   */
  public function getWeight();

  /**
   * Sets the recurring engine weight.
   *
   * @param int $weight
   *   The recurring engine weight.
   *
   * @return $this
   */
  public function setWeight($weight);

  /**
   * Gets the recurring engine plugin.
   *
   * @return \Drupal\commerce_recurring\Plugin\Commerce\RecurringEngine\RecurringEngineInterface
   *   The recurring engine plugin.
   */
  public function getPlugin();

  /**
   * Gets the recurring engine plugin ID.
   *
   * @return string
   *   The recurring engine plugin ID.
   */
  public function getPluginId();

  /**
   * Sets the recurring engine plugin ID.
   *
   * @param string $plugin_id
   *   The recurring engine plugin ID.
   *
   * @return $this
   */
  public function setPluginId($plugin_id);

  /**
   * Gets the recurring engine plugin configuration.
   *
   * @return string
   *   The recurring engine plugin configuration.
   */
  public function getPluginConfiguration();

  /**
   * Sets the recurring engine plugin configuration.
   *
   * @param array $configuration
   *   The recurring engine plugin configuration.
   *
   * @return $this
   */
  public function setPluginConfiguration(array $configuration);

  // @TODO: Figure out what other methods (from the plugin?) we might
  // want to have on the config entity itself, probably for the sake
  // of convenience? Not sure why gateways wrap those functions exactly.

}

