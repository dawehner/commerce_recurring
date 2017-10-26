<?php

namespace Drupal\commerce_recurring;

use Drupal\advancedqueue\Job;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Determines recurring orders which have ended already.
 *
 * For all these orders we fill create queue entries to refresh and close them.
 */
class RecurringCron {

  /**
   * The commerce_order_item storage.
   *
   * @var \Drupal\commerce\CommerceContentEntityStorage
   */
  protected $orderStorage;

  /**
   * The current time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The commerce_recurring_order_renew queue.
   *
   * @var \Drupal\advancedqueue\Entity\QueueInterface
   */
  protected $recurringQueue;

  /**
   * The commerce_recurring_order_close queue.
   *
   * @var \Drupal\advancedqueue\Entity\QueueInterface
   */
  protected $recurringOrderCloseQueue;

  /**
   * RecurringCron constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TimeInterface $time) {
    $this->orderStorage = $entity_type_manager->getStorage('commerce_order');
    $this->time = $time;
    $this->recurringQueue = $entity_type_manager->getStorage('advancedqueue_queue')->load('commerce_recurring');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('datetime.time')
    );
  }

  public function cron() {
    $ended_orders = $this->getEndedRecurringOrders();
    foreach ($ended_orders as $order) {
      $this->recurringQueue->enqueueJob(Job::create('commerce_recurring_order_close', [
        'title' => 'Close recurring order #' . $order->id(),
        'order_id' => $order->id(),
        ]));
      $this->recurringQueue->enqueueJob(Job::create('commerce_recurring_order_renew', [
        'title' => 'Renew recurring order #' . $order->id(),
        'order_id' => $order->id(),
      ]));
    }
  }

  /**
   * @return \Drupal\commerce_order\Entity\OrderInterface[]
   */
  protected function getEndedRecurringOrders() {
    // @todo Should we maybe just handle a bunch of these expired orders at a
    //   time?
    $result = $this->orderStorage->getQuery()
      ->condition('type', 'recurring')
      // @todo Should we filter by the state of the order?
      ->condition('ended', $this->time->getRequestTime(), '<')
      ->condition('state', 'canceled', '<>')
      ->execute();
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface[] $order_items */
    $orders = $this->orderStorage->loadMultiple($result);
    return $orders;
  }

}
