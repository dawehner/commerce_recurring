<?php

namespace Drupal\commerce_recurring\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the recurring engine plugin annotation object.
 *
 * Plugin namespace: Plugin\Commerce\RecurringEngine.
 *
 * @see plugin_api
 *
 * @Annotation
 */
class CommerceRecurringEngine extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The recurring engine label.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;
}

