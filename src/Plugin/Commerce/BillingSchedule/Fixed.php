<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;

/**
 * @CommerceBillingCycle(
 *   id = "fixed",
 *   label = @Translation("Fixed interval"),
 * )
 */
class Fixed extends BillingScheduleBase {

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
        'quarter' => 'Quarter',
        'half-year' => 'Half-year',
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
   * {@inheritdoc}
   */
  public function getFirstBillingCycle(DrupalDateTime $startTime) {
    $startTime = clone $startTime;

    switch ($this->configuration['unit']) {
      case 'hour':
        $startTime->setTime($startTime->format('G'), 0);
        break;
      case 'day':
        $startTime->modify('midnight');
        break;
      case 'week':
        $startTime->modify('monday');
        break;
      case 'month':
        $startTime->modify('first day of this month');
        break;
      case 'quarter':
        $month = $startTime->format('n');

        // @todo is there some better alternative out there?
        if ($month < 4) {
          $startTime->modify('first day of january');
        }
        elseif ($month > 3 && $month < 7) {
          $startTime->modify('first day of april');
        }
        elseif ($month > 6 && $month < 10) {
          $startTime->modify('first day of july');
        }
        elseif ($month > 9) {
          $startTime->modify('first day of october');
        }

        break;
      case 'half-year':
        $month = $startTime->format('n');
        if ($month < 7) {
          $startTime->modify('first day of january');
        }
        else {
          $startTime->modify('first day of july');
        }
        break;
      case 'year':
        $startTime->modify('first day of january');
        break;
      default:
        throw new \Exception('You missed a case ...');
    }

    $endDate = clone $startTime;
    $endDate = $this->modifyTime($endDate, $this->configuration['number'], $this->configuration['unit']);

    return new BillingCycle(0, $startTime, $endDate);
  }

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
      case 'quarter':
        $months = $number * 3;
        $date->modify("+{$months} month");
        break;
      case 'half-year':
        $months = $number * 6;
        $date->modify("+{$months} month");
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
    return new BillingCycle($cycle->getIndex() + 1, $cycle->getEndDateTime(), $this->modifyTime($cycle->getEndDateTime(), $this->configuration['number'], $this->configuration['unit']));
  }

}
