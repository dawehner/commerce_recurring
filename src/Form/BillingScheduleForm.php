<?php

namespace Drupal\commerce_recurring\Form;

use Drupal\commerce\Form\CommercePluginEntityFormBase;
use Drupal\commerce_recurring\BillingScheduleManager;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BillingScheduleForm extends CommercePluginEntityFormBase {

  /**
   * The billing schedule plugin manager.
   *
   * @var \Drupal\commerce_recurring\BillingScheduleManager
   */
  protected $pluginManager;

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
    /** @var \Drupal\commerce_recurring\Entity\BillingScheduleInterface $billing_schedule */
    $billing_schedule = $this->entity;
    $plugins = array_column($this->pluginManager->getDefinitions(), 'label', 'id');
    asort($plugins);

    // Use the first available plugin as the default value.
    if (!$billing_schedule->getPluginId()) {
      $plugin_ids = array_keys($plugins);
      $plugin = reset($plugin_ids);
      $billing_schedule->setPluginId($plugin);
    }
    // The form state will have a plugin value if #ajax was used.
    $plugin = $form_state->getValue('plugin', $billing_schedule->getPluginId());
    // Pass the plugin configuration only if the plugin hasn't been changed via #ajax.
    $plugin_configuration = $billing_schedule->getPluginId() == $plugin ? $billing_schedule->getPluginConfiguration() : [];

    $wrapper_id = Html::getUniqueId('billing-schedule-form');
    $form['#tree'] = TRUE;
    $form['#prefix'] = '<div id="' . $wrapper_id . '">';
    $form['#suffix'] = '</div>';

    $form['label'] = [
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $billing_schedule->label(),
      '#required' => TRUE,
      '#size' => 30,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $billing_schedule->id(),
      '#machine_name' => [
        'exists' => [BillingSchedule::class, 'load'],
        'source' => ['label'],
      ],
    ];
    $form['display_label'] = [
      '#type' => 'textfield',
      '#title' => t('Display label'),
      '#description' => t('Used to identify the billing schedule on the frontend.'),
      '#default_value' => $billing_schedule->getDisplayLabel(),
    ];
    $form['plugin'] = [
      '#type' => 'radios',
      '#title' => $this->t('Plugin'),
      '#options' => $plugins,
      '#default_value' => $plugin,
      '#required' => TRUE,
      '#disabled' => !$billing_schedule->isNew(),
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

    $form['billing_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Billing type'),
      '#options' => [
        'prepaid' => $this->t('Prepaid'),
        'postpaid' => $this->t('Postpaid'),
      ],
      '#default_value' => $this->entity->getBillingType(),
    ];

    $form['status'] = [
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#options' => [
        FALSE => $this->t('Disabled'),
        TRUE => $this->t('Enabled'),
      ],
      '#default_value' => $billing_schedule->status(),
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

    /** @var \Drupal\commerce_recurring\Entity\BillingScheduleInterface $billing_schedule */
    $billing_schedule = $this->entity;
    $billing_schedule->setPluginConfiguration($form_state->getValue(['configuration']));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    drupal_set_message($this->t('Saved the @label billing schedule.', ['@label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_billing_schedule.collection');
  }

}
