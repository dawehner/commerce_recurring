<?php

namespace Drupal\commerce_recurring\Plugin\Field\FieldWidget;

use Drupal\commerce\Plugin\Field\FieldWidget\PluginSelectWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'commerce_recurring_plugin_single_disabled' widget.
 *
 * @FieldWidget(
 *   id = "commerce_recurring_plugin_single_disabled",
 *   label = @Translation("Plugin select (disabled when there is just one option)"),
 *   field_types = {
 *     "commerce_plugin_item",
 *     "commerce_plugin_item:commerce_subscription_type"
 *   },
 * )
 */
class PluginWithSingleDisabledWidget extends PluginSelectWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Hide the plugin selection if there is just one available anyway.
    $options = $element['target_plugin_id']['#options'];
    if (count($options) === 1) {
      reset($options);
      $element['target_plugin_id'] = [
        '#type' => 'value',
        '#value' => key($options),
      ];
    }

    return $element;
  }

}
