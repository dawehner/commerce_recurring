<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce_recurring\BillingCycle;

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

  /**
   * {@inheritdoc}
   */
  public function collectCharges(BillingCycle $billing_cycle) {
    return [];
  }


}
