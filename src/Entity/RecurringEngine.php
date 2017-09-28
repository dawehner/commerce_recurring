<?php

namespace Drupal\commerce_recurring\Entity;

use Drupal\commmerce\CommerceSinglePluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the recurring engine entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_recurring_engine",
 *   label = @Translation("Recurring engine"),
 *   label_collection = @Translation("Recurring engines"),
 *   label_singular = @Translation("recurring engine"),
 *   label_plural = @Translation("recurring engines"),
 *   label_count = @PluralTranslation(
 *     singular = "@countrecurring engines",
 *     plural = "@count recurring engines",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_recurring\RecurringEngineListBuilder",
 *     "storage" = "Drupal\commerce_recurring\RecurringEngineStorage",
 *     "form" = {
 *       "add" = "Drupal\commerce_recurring\Form\RecurringEngineForm",
 *       "edit" = "Drupal\commerce_recurring\Form\RecurringEngineForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer commerce_recurring_engine",
 *   config_prefix = "commerce_recurring_engine",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "weight" = "weight",
 *     "status" = "status",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "weight",
 *     "status",
 *     "plugin",
 *     "configuration",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/recurring-engines/add",
 *     "edit-form" = "/admin/commerce/config/recurring-engines/manage/{commerce_recurring_engine}",
 *     "delete-form" = "/admin/commerce/config/recurring-engines/manage/{commerce_recurring_engine}/delete",
 *     "collection" =  "/admin/commerce/config/recurring-engines"
 *   }
 * )
 */
class RecurringEngine extends ConfigEntityBase implements RecurringEngineInterface {

  /**
   * The recurring engine ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The recurring engine label.
   *
   * @var string
   */
  protected $label;

  /**
   * The recurring engine weight.
   *
   * @var int
   */
  protected $weight;

  /**
   * The plugin ID.
   *
   * @var string
   */
  protected $plugin;

  /**
   * The plugin configuration.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * The plugin collection that holds the recurring engine plugin.
   *
   * @var \Drupal\commerce\CommerceSinglePluginCollection
   */
  protected $pluginCollection;

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
    return $weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    return $this->getPluginCollection()->get($this->plugin);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return $this->plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setPluginId($plugin_id) {
    $this->plugin = $plugin_id;
    $this->configuration = [];
    $this->pluginCollection = NULL;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setPluginConfiguration(array $configuration) {
    $this->configuration = $configuration;
    $this->pluginCollection = NULL;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'configuration' => $this->getPluginCollection(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function set($property_name, $value) {
    // Invoke the setters to clear related properties.
    if ($property_name == 'plugin') {
      $this->setPluginId($value);
    }
    elseif ($property_name == 'configuration') {
      $this->setPluginConfiguration($value);
    }
    else {
      return parent::set($property_name, $value);
    }
  }

  /**
   * Gets the plugin collection that holds the recurring engine plugin.
   *
   * Ensures the plugin collection is initialized before returning it.
   *
   * @return \Drupal\commerce\CommerceSinglePluginCollection
   *   The plugin collection.
   */
  protected function getPluginCollection() {
    if (!$this->pluginCollection) {
      $plugin_manager = \Drupal::service('plugin.manager.commerce_recurring_engine');
      $this->pluginCollection = new CommerceSinglePluginCollection($plugin_manager, $this->plugin, $this->configuration, $this->id);
    }
    return $this->pluginCollection;
  }

}

