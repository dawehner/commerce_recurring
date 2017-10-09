<?php

namespace Drupal\commerce_recurring\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Provides a default billing cycle formatter
 *
 * @FieldFormatter(
 *   id = "commerce_billing_cycle_default",
 *   module = "commerce_recurring",
 *   label = @Translation("Billing cycle"),
 *   field_types = {
 *     "commerce_billing_cycle"
 *   },
 *   quickedit = {
 *     "editor" = "disabled"
 *   }
 * )
 */
class BillingCycleDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $build = [];
    /** @var \Drupal\commerce_recurring\Plugin\Field\FieldType\BillingCycleItem $item */
    foreach ($items as $item) {
      $build[] = [
        '#theme' => 'item_list',
        '#items' => [
          $item->toBillingCycle()->getIndex(),
          $item->toBillingCycle()->getStartDateTime()->format('c'),
          $item->toBillingCycle()->getEndDateTime()->format('c'),
        ]
      ];
    }
    return $build;
  }

}
