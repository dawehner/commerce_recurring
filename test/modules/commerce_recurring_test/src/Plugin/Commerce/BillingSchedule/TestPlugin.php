<?php

namespace Drupal\commerce_recurring_test\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\BillingScheduleBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;

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
  public function getFirstBillingCycle(DrupalDateTime $startTime) {
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
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['key'] = $values['key'];
    }
  }

}
