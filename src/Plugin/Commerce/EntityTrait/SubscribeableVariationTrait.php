<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\EntityTrait;

use Drupal\commerce\BundleFieldDefinition;
use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Provides the first entity trait.
 *
 * @CommerceEntityTrait(
 *   id = "subscribeable_variation",
 *   label = @Translation("Subscribeable Variation"),
 *   entity_types = {"commerce_product_variation"}
 * )
 */
class SubscribeableVariationTrait extends EntityTraitBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];
    $fields['billing_schedule'] = BundleFieldDefinition::create('entity_reference')
      ->setLabel(t('Billing schedules'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'commerce_billing_schedule')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['subscription_type'] = BundleFieldDefinition::create('commerce_plugin_item:commerce_subscription_type')
      ->setLabel(t('Subscription type'))
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'type' => 'commerce_plugin_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
