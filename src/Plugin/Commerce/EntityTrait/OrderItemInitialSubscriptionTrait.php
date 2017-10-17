<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\EntityTrait;

use Drupal\commerce\BundleFieldDefinition;
use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitBase;

/**
 * Provides the first entity trait.
 *
 * @CommerceEntityTrait(
 *   id = "order_item_initial_subscription",
 *   label = @Translation("Order item initial subscription"),
 *   entity_types = {"commerce_order_item"}
 * )
 */
class OrderItemInitialSubscriptionTrait extends EntityTraitBase {

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
