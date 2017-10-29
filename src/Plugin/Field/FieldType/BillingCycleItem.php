<?php

namespace Drupal\commerce_recurring\Plugin\Field\FieldType;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\commerce_recurring\BillingCycle as BillingCycleObject;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'commerce_price' field type.
 *
 * @FieldType(
 *   id = "commerce_billing_cycle",
 *   label = @Translation("Billing cycle"),
 *   description = @Translation("Stores a a billing cycle"),
 *   category = @Translation("Commerce"),
 *   default_widget = "commerce_billing_cycle_default",
 *   default_formatter = "commerce_billing_cycle_default",
 * )
 */
class BillingCycleItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['start_date'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Start date value'))
      ->setRequired(TRUE);
    $properties['end_date'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('End date value'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'start_date' => [
          'description' => 'The start date value.',
          'type' => 'varchar',
          'length' => 20,
        ],
        'end_date' => [
          'description' => 'The end date value.',
          'type' => 'varchar',
          'length' => 20,
        ],
      ],
      'indexes' => [
        'range' => ['start_date', 'end_date'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $start_value = $this->get('start_date')->getValue();
    $end_value = $this->get('start_date')->getValue();
    return ($start_value === NULL || $start_value === '') && ($end_value === NULL || $end_value === '');
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    // Allow callers to pass a Price value object as the field item value.
    if ($values instanceof BillingCycleObject) {
      $values = [
        'start_date' => $values->getStartDate()->format('c'),
        'end_date' => $values->getEndDate()->format('c'),
      ];
    }

    if (isset($values['start_date']) && ($values['start_date'] instanceof DrupalDateTime || $values['start_date'] instanceof \DateTime)) {
      $values['start_date'] = $values['start_date']->format('c');
    }
    if (isset($values['end_date']) && ($values['end_date'] instanceof DrupalDateTime || $values['end_date'] instanceof \DateTime)) {
      $values['end_date'] = $values['end_date']->format('c');
    }

    parent::setValue($values, $notify);
  }

  /**
   * Gets the billing cycle value object for the current field item.
   *
   * @reutrn \Drupal\commerce_recurring\BillingCycle
   *   The billing cycle object.
   */
  public function toBillingCycle() {
    return new BillingCycleObject(new DrupalDateTime($this->start_date), new DrupalDateTime($this->end_date));
  }

}
