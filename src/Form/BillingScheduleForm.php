<?php

namespace Drupal\commerce_recurring\Form;

use Drupal\commerce\Form\CommercePluginEntityFormBase;
use Drupal\commerce_recurring\BillingScheduleManager;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BillingScheduleForm extends CommercePluginEntityFormBase {

  /**
   * The billing schedule plugin manager.
   *
   * @var \Drupal\commerce_recurring\BillingScheduleManager
   */
  protected $pluginManager;

  /**
   * @var \Drupal\commerce_recurring\Entity\BillingScheduleInterface
   */
  protected $entity;

  /**
   * Constructs a new BillingScheduleForm object.
   *
   * @param \Drupal\commerce_recurring\BillingScheduleManager $plugin_manager
   *   The billing schedule plugin manager.
   */
  public function __construct(BillingScheduleManager $plugin_manager) {
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.commerce_billing_schedule')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $gateway = $this->entity;
    $plugins = array_column($this->pluginManager->getDefinitions(), 'label', 'id');
    asort($plugins);

    // Use the first available plugin as the default value.
    if (!$gateway->getPluginId()) {
      $plugin_ids = array_keys($plugins);
      $plugin = reset($plugin_ids);
      $gateway->setPluginId($plugin);
    }
    // The form state will have a plugin value if #ajax was used.
    $plugin = $form_state->getValue('plugin', $gateway->getPluginId());
    // Pass the plugin configuration only if the plugin hasn't been changed via #ajax.
    $plugin_configuration = $gateway->getPluginId() == $plugin ? $gateway->getPluginConfiguration() : [];

    $form['label'] = [
      '#title' => t('Admin label'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->label(),
      '#description' => t('The human readable name of the billing schedule'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['display_label'] = [
      '#type' => 'textfield',
      '#title' => t('Display label'),
      '#description' => t('Used to identify the applied tax in order summaries.'),
      '#default_value' => $this->entity->getDisplayLabel(),
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => [BillingSchedule::class, 'load'],
        'source' => ['label'],
      ],
      '#description' => t('An ID for the billing schedule.'),
    ];

    $wrapper_id = Html::getUniqueId('billing-schedule-plugin-form');
    $form['#tree'] = TRUE;
    $form['#prefix'] = '<div id="' . $wrapper_id . '">';
    $form['#suffix'] = '</div>';
    $form['#tree'] = TRUE;

    $form['plugin'] = [
      '#type' => 'radios',
      '#title' => $this->t('Plugin'),
      '#options' => $plugins,
      '#default_value' => $plugin,
      '#required' => TRUE,
      '#disabled' => !$gateway->isNew(),
      '#ajax' => [
        'callback' => '::ajaxRefresh',
        'wrapper' => $wrapper_id,
      ],
    ];

      $form['configuration'] = [
      '#type' => 'commerce_plugin_configuration',
      '#plugin_type' => 'commerce_billing_schedule',
      '#plugin_id' => $plugin,
      '#default_value' => $plugin_configuration,
    ];

    $form['status'] = [
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#options' => [
        FALSE => $this->t('Disabled'),
        TRUE => $this->t('Enabled'),
      ],
      '#default_value' => $gateway->status(),
    ];

    return $this->protectPluginIdElement($form);
  }

  /**
   * Ajax callback.
   */
  public static function ajaxRefresh(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->entity->setPluginConfiguration($form_state->getValue(['configuration']));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    drupal_set_message($this->t('Billing schedule @label created', ['@label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_billing_schedule.collection');

    return $result;
  }


}
