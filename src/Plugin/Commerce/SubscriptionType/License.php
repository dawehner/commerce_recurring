<?php

namespace Drupal\commerce_recurring\Plugin\Commerce\SubscriptionType;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\commerce_recurring\Charge;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;

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
  public function collectCharges(BillingCycle $billing_cycle, SubscriptionInterface $subscription) {
    return [new Charge($subscription->getAmount(), 'Label todo', $billing_cycle->getStartDate(), $billing_cycle->getEndDate())];
  }

}
