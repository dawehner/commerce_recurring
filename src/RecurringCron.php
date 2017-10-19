<?php

namespace Drupal\commerce_recurring;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Queue\QueueFactory;
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
   * The commerce_recurring_order_refresh queue.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $recurringOrderRefreshQueue;

  /**
   * The commerce_recurring_order_close queue.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $recurringOrderCloseQueue;

  /**
   * RecurringCron constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TimeInterface $time, QueueFactory $queue_factory) {
    $this->orderStorage = $entity_type_manager->getStorage('commerce_order');
    $this->time = $time;
    $this->recurringOrderRefreshQueue = $queue_factory->get('commerce_recurring_order_refresh');
    $this->recurringOrderCloseQueue = $queue_factory->get('commerce_recurring_order_close');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('datetime.time'),
      $container->get('queue')
    );
  }

  public function cron() {
    $ended_orders = $this->getEndedRecurringOrders();
    foreach ($ended_orders as $order) {
      $this->recurringOrderCloseQueue->createItem([
        'order_id' => $order->id(),
      ]);
      $this->recurringOrderRefreshQueue->createItem([
        'order_id' => $order->id(),
      ]);
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
      ->execute();
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface[] $order_items */
    $orders = $this->orderStorage->loadMultiple($result);
    return $orders;
  }

}
