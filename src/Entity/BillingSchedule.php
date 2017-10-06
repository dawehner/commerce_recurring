<?php

namespace Drupal\commerce_recurring\Entity;

use Drupal\commerce\CommerceSinglePluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the billing schedule entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_billing_schedule",
 *   label = @Translation("Billing schedule"),
 *   label_collection = @Translation("Billing schedule"),
 *   label_singular = @Translation("billing schedule"),
 *   label_plural = @Translation("billing schedule"),
 *   label_count = @PluralTranslation(
 *     singular = "@count billing schedule",
 *     plural = "@count billing schedules",
 *   ),
 *   handlers = {
 *     "list_builder" = "\Drupal\Core\Entity\EntityListBuilder",
 *     "storage" = "\Drupal\Core\Config\Entity\ConfigEntityStorage",
 *     "form" = {
 *       "add" = "\Drupal\commerce_recurring\Form\BillingScheduleForm",
 *       "edit" = "\Drupal\commerce_recurring\Form\BillingScheduleForm",
 *       "delete" = "\Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   bundle_plugin_type = "commerce_billing_schedule",
 *   admin_permission = "administer commerce_billing_schedule",
 *   config_prefix = "commerce_billing_schedule",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "display_label",
 *     "status",
 *     "plugin",
 *     "configuration",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/payment/billing-schedule/add",
 *     "edit-form" = "/admin/commerce/config/payment/billing-schedule/manage/{commerce_billing_schedule}",
 *     "delete-form" = "/admin/commerce/config/payment/billing-schedule/manage/{commerce_billing_schedule}/delete",
 *     "collection" =  "/admin/commerce/config/billing-schedule"
 *   }
 * )
 */
class BillingSchedule extends ConfigEntityBase implements BillingScheduleInterface {

  /**
   * The billing schedule ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The billing schedule label.
   *
   * @var string
   */
  protected $label;

  /**
   * The label displayed to the customer.
   *
   * @var string
   */
  protected $display_label;

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
  public function getDisplayLabel() {
    return $this->display_label;
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
    if ($property_name === 'plugin') {
      $this->setPluginId($value);
    }
    elseif ($property_name === 'configuration') {
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
      $plugin_manager = \Drupal::service('plugin.manager.commerce_billing_schedule');
      $this->pluginCollection = new CommerceSinglePluginCollection($plugin_manager, $this->plugin, $this->configuration, $this->id);
    }
    return $this->pluginCollection;
  }

}

