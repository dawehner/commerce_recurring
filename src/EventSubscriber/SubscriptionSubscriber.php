<?php

namespace Drupal\commerce_recurring\EventSubscriber;

use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['commerce_subscription.activate.post_transition'] = ['onActivate'];
    return $events;
  }

  /**
   * Creates recurring orders when subscriptions are activated.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   */
  public function onActivate(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_recurring\Entity\SubscriptionInterface $subscription */
    $subscription = $event->getEntity();
    $subscription->getType()->createRecurringOrder($subscription);
  }

}
