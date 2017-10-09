<?php

namespace Drupal\commerce_recurring\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'commerce_billing_cycle' widget.
 *
 * @FieldWidget(
 *   id = "commerce_billing_cycle_default",
 *   label = @Translation("Billing cycle"),
 *   field_types = {
 *     "commerce_billing_cycle"
 *   },
 *  )
 */
class BillingCycleDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['index'] += [
      '#type' => 'number',
      '#default_value' => $items->get($delta)->index,
    ];
    $element['start_date'] += [
      '#type' => 'textfield',
      '#default_value' => $items->get($delta)->start_date,
    ];
    $element['end_date'] += [
      '#type' => 'textfield',
      '#default_value' => $items->get($delta)->end_date,
    ];

    return $element;
  }

}
