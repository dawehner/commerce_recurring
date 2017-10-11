<?php

namespace Drupal\commerce_recurring\EventSubscriber;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_recurring\Entity\Subscription;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @todo this needs seriously a test!
 */
class OrderSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * OrderSubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['commerce_order.place.pre_transition'] = 'onPlaceTransition';
    return $events;
  }

  /**
   * Create subscription when orders with billing schedules attached are placed.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The transition event.
   */
  public function onPlaceTransition(WorkflowTransitionEvent $event) {
    // @todo Is this the right condition?
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    $payment_method = $order->get('payment_method')->entity;
    if (empty($payment_method)) {
      return;
    }

    foreach ($order->getItems() as $item) {
      // @todo What do we do with other entity types?
      if (($purchased_entity = $item->getPurchasedEntity()) && $purchased_entity instanceof ProductVariationInterface && $purchased_entity->hasField('billing_schedule') && $purchased_entity->hasField('subscription_type')) {

        foreach ($purchased_entity->get('billing_schedule')->referencedEntities() as $billing_schedule) {
    
          $subscription = Subscription::create([
            'type' => $purchased_entity->get('subscription_type')->target_plugin_id,
            'state' => 'active',
          ]);
          $subscription->setBillingSchedule($billing_schedule);
          $subscription->setCustomer($order->getCustomer());
          $subscription->setPurchasedEntity($purchased_entity);
          $subscription->setPaymentMethod($payment_method);
          // @todo Maybe this should be the unit price?
          $subscription->setAmount($item->getTotalPrice());

          $subscription->save();
        }
      }
    }
  }

}
