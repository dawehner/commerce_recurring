<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\EntityTrait;

use Drupal\commerce\BundleFieldDefinition;
use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitBase;

/**
 * Provides the first entity trait.
 *
 * @CommerceEntityTrait(
 *   id = "scheduleable_order",
 *   label = @Translation("Scheduleable Order"),
 *   entity_types = {"commerce_order"}
 * )
 */
class ScheduleableOrderTrait extends EntityTraitBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];
    $fields['billing_schedule'] = BundleFieldDefinition::create('entity_reference')
      ->setLabel(t('Billing schedules'))
      ->setCardinality(1)
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'commerce_billing_schedule')
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE);


    return $fields;
  }

}
