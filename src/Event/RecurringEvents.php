<?php

namespace Drupal\commerce_recurring\Event;

final class RecurringEvents {

  /**
   * Name of the event fired when a subscription requests a recurring engine from storage.
   *
   * @Event
   *
   * @see \Drupal\commerce_recurring\Event\SelectRecurringEngineEvent
   */
  const SELECT_RECURRING_ENGINE = 'commerce_recurring.select_recurring_engine';

}

