<?php

namespace Drupal\commerce_recurring_test\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\BillingScheduleBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * @CommerceBillingSchedule(
 *   id = "test_plugin",
 *   label = "Test label"
 * )
 */
class TestPlugin extends BillingScheduleBase {

  /**
   * {@inheritdoc}
   */
  public function getBillingCycle(AccountInterface $account, DrupalDateTime $startTime) {
    return new BillingCycle('My first billing cycle', new DrupalDateTime(), new DrupalDateTime());
  }

  /**
   * {@inheritdoc}
   */
  public function getNextBillingCycle(BillingCycle $cycle) {
    return new BillingCycle('My first billing cycle', new DrupalDateTime(), new DrupalDateTime());
  }

  /**
   * {@inheritdoc}
   */
  public function renewCycle(BillingCycle $cycle) {
  }

  /**
   * {@inheritdoc}
   */
  public function closeCycle(BillingCycle $cycle) {
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['key' => 'value'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key'),
      '#default_value' => $this->configuration['key'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['key'] = $form_state->getValue('key');
  }

}
