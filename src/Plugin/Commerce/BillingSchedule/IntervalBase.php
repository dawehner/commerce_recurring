<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class to share code between fixed and rolling billing schedules.
 */
abstract class IntervalBase extends BillingScheduleBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'number' => 1,
      'unit' => 'month',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['number'] = [
      '#type' => 'number',
      '#title' => $this->t('Number'),
      '#default_value' => $this->configuration['interval'],
    ];

    $form['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Period'),
      '#options' => [
        'hour' => 'Hour',
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
        'year' => 'Year',
      ],
      '#default_value' => $this->configuration['interval'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['number'] = $form_state->getValue('number');
    $this->configuration['unit'] = $form_state->getValue('unit');
  }

  /**
   * Modifies a time by a certain number of units.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date and time.
   * @param int $number
   *   The amount of units.
   * @param string $unit
   *   The actual unit.
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   THe changed date object.
   *
   * @throws \Exception
   *   Thrown when an invalid unit got passed in.
   */
  protected function modifyTime(DrupalDateTime $date, $number, $unit) {
    $date = clone $date;
    switch ($unit) {
      case 'hour':
        $date->modify("+{$number} hour");
        break;

      case 'day':
        $date->modify("+{$number} day");
        break;

      case 'week':
        $date->modify("+{$number} week");
        break;

      case 'month':
        $date->modify("+{$number} month");
        break;

      case 'year':
        $date->modify("+{$number} year");
        break;

      default:
        throw new \Exception('You missed a case ...');
    }
    return $date;
  }

  /**
   * {@inheritdoc}
   */
  public function getNextBillingCycle(BillingCycle $cycle) {
    // @todo Should we have some + / - second offset?
    return new BillingCycle($cycle->getEndDate(), $this->modifyTime($cycle->getEndDate(), $this->configuration['number'], $this->configuration['unit']));
  }

  /**
   * Determines the initial start time, which depend on the billing type.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $start_time
   *   The start time.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The initial time.
   */
  protected function determineFirstStartTime(DrupalDateTime $start_time) {
    $start_time = clone $start_time;
    $billing_schedule = BillingSchedule::load($this->entityId);
    if ($billing_schedule->getBillingType() === 'prepaid') {
      $start_time = $this->modifyTime($start_time, $this->configuration['number'], $this->configuration['unit']);
    }
    return $start_time;
  }

}
