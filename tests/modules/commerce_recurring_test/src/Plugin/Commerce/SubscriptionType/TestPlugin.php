<?php

namespace Drupal\commerce_recurring_test\Plugin\Commerce\SubscriptionType;

use Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType\SubscriptionTypeBase;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @CommerceSubscriptionType(
 *   id = "test_plugin",
 *   label = @Translation("Test plugin"),
 * )
 */
class TestPlugin extends SubscriptionTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];
//    $fields['test_plugin'] = BaseFieldDefinition::create('string')
//      ->setLabel(t('Test Plugin'))
//      ->setRequired(TRUE)
//      ->setTranslatable(TRUE)
//      ->setRevisionable(TRUE)
//      ->setSetting('max_length', 255)
//      ->setDisplayOptions('view', [
//        'label' => 'hidden',
//        'type' => 'string',
//        'weight' => -5,
//      ])
//      ->setDisplayOptions('form', [
//        'type' => 'string_textfield',
//        'weight' => -5,
//      ])
//      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }


}
