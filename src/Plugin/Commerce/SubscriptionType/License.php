<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

/**
 * @CommerceSubscriptionType(
 *   id = "license",
 *   label = @translation("License"),
 * )
 */
class License extends SubscriptionTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];
    return $fields;
  }

}
