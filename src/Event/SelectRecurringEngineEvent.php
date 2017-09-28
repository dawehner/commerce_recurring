<?php

namespace Drupal\commerce_recurring\Event;

// use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the event for filtering the available payment gateways.
 *
 * @see \Drupal\commerce_payment\Event\PaymentEvents
 */
class SelectRecurringEngineEvent extends Event {

  /**
   * The available recurring engines.
   *
   * @var \Drupal\commerce_recurring\Entity\RecurringEngineInterface[]
   */
  protected $availableEngines;

  /**
   * The selected recurring engine.
   *
   * @var \Drupal\commerce_recurring\Entity\RecurringEngineInterface
   */
  protected $selectedEngine;

  /**
   * The subscription.
   *
   * @var \Drupal\commerce_recurring\Entity\SubscriptionInterface
   */
  protected $subscription;

  /**
   * Constructs a new SelectRecurringEngineEvent object.
   *
   * @param \Drupal\commerce_recurring\Entity\RecurringEngineInterface[] $availableEngines
   *   The available recurring engines.
   * @param \Drupal\commerce_recurring\Entity\RecurringEngineInterface $selectedEngine
   *   The initially selected recurring engine.
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription
   *   The subscription.
   */
  public function __construct(array $availableEngines, RecurringEngineInterface $selectedEngine, SubscriptionInterface $subscription) {
    $this->availableEngines = $engines;
    $this->selectedEngine = $selectedEngine;
    $this->subscription = $subscription;
  }

  /**
   * Gets the available recurring engines.
   *
   * @return \Drupal\commerce_recurring\Entity\RecurringEngineInterface[]
   *   The payment gateways.
   */
  public function getAvailableEngines() {
    return $this->availableEngines;
  }

  /**
   * Sets the available recurring engines. Use this method to override
   * e.g. the access control layer and make new engines available to
   * other events. (It is expected that this will be used rarely.
   *
   * @param \Drupal\commerce_recurring\Entity\RecurringEngineInterface[] $engines
   *   A new array of available recurring engines.
   *
   * @return $this
   */
  public function setAvailableEngines(array $engines) {
    $this->availableEngines = $engines;
    return $this;
  }

  /**
   * Get the currently selected engine.
   *
   * @return \Drupal\commerce_recurring\Entity\RecurringEngineInterface
   */
  public function getSelectedEngine() {
    return $this->selectedEngine;
  }

  /**
   * Set the currently selected engine.
   *
   * @param \Drupal\commerce_recurring\Entity\RecurringEngineInterface $engine
   *
   * @return $this
   */
  public function setSelectedEngine(RecurringEngineInterface $engine) {
    $this->selectedEngine = $engine;
    return $this;
  }

  /**
   * Gets the subscription.
   *
   * @return \Drupal\commerce_recurring\Entity\SubscriptionInterface
   *   The subscription.
   */
  public function getSubscription() {
    return $this->subscription;
  }

}

