<?php

namespace Drupal\commerce_recurring\Field\FieldWidget;

use Drupal\commerce\Plugin\Field\FieldWidget\PluginSelectWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'commerce_plugin_select' widget.
 *
 * @FieldWidget(
 *   id = "commerce_recurring_plugin_single_disabled",
 *   label = @Translation("Plugin select (disabled when there is just one option)"),
 *   field_types = {
 *     "commerce_plugin_item"
 *   },
 * )
 */
class PluginWithSingleDisabledWidget extends PluginSelectWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    return $element;
  }

}
