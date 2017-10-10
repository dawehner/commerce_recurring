<?php

namespace Drupal\commerce_recurring\EventSubscriber;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_recurring\Entity\Subscription;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderPlacedCreateSubscriptionsSubscriber implements EventSubscriberInterface {

  /**
   * Create subscription when orders with billing schedules attached are placed.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The transition event.
   */
  public function onPlaceTransition(WorkflowTransitionEvent $event) {
    // @todo Is this the right condition?
    if (($order = $event->getEntity()) && $order instanceof OrderInterface) {
      if ($event->getFromState() ->getId() !== 'completed' && $event->getToState() ->getId() === 'completed') {
        foreach ($order->getItems() as $item) {
          // @todo What do we do with other entity types?
          if (($purchased_entity = $item->getPurchasedEntity()) && $purchased_entity instanceof ProductVariationInterface && $purchased_entity->hasField('billing_schedule') && $purchased_entity->hasField('subscription_type')) {
            foreach ($purchased_entity->get('billing_schedule')->referencedEntities() as $billing_schedule) {
              $subscription = Subscription::create([
                'type' => $purchased_entity->get('subscription_type')->target_id,
                'state' => 'pending',
              ]);
              $subscription->set('billing_schedule', $billing_schedule);
              $subscription->setCustomer($order->getCustomer());
              $subscription->set('purchased_entity', $purchased_entity);
              $subscription->set('state', 'pending');

              // @todo Figure out how to retrieve the payment method.
              // @todo Maybe this should be the unit price?
              $subscription->setAmount($item->getTotalPrice());
              // @todo Figure out the started and ended fields.

              $subscription->save();
            }
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['commerce_order.place.pre_transition'] = 'onPlaceTransition';
    return $events;
  }

}
