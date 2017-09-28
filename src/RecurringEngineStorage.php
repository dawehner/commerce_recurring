<?php

namespace Drupal\commerce_recurring;

use Drupal\commerce_recurring\Entity\SubscriptionInterface;
use Drupal\commerce_recurring\Event\SelectRecurringEngineEvent;
use Drupal\commerce_recurring\Event\RecurringEvents;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines the recurring engine storage.
 */
class RecurringEngineStorage extends ConfigEntityStorage implements RecurringEngineStorageInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a RecurringEngineStorage object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory, UuidInterface $uuid_service, LanguageManagerInterface $language_manager, EventDispatcherInterface $event_dispatcher) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager);
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function selectRecurringEngine(SubscriptionInterface $subscription) {
    /** @var \Drupal\commerce_recurring\Entity\RecurringEngineInterface[] $engines */
    $engines = $this->loadByProperties(['status' => TRUE]);
    // @TODO: Some access control necessary here or will it "just work"?
    // Each subscription bundle plugin will have a way of selecting its default
    // recurring engine.
    $default = $subscription->getType->getDefaultRecurringEngine();
    // Allow the selected engine to be possibly changed via code.
    $event = new SelectRecurringEngineEvent($engines, $default, $subscription);
    $this->eventDispatcher->dispatch(RecurringEvents::SELECT_RECURRING_ENGINE, $event);
    $selected = $event->getSelectedEngine();

    // Return the selected engine.
    return $selected;
  }

  // @TODO: Figure out if we need this loader functions. We anticipate
  // that subscribable entities will refer to a recurring engine somehow
  // so these won't be necessary, but I'm leaving them here for now,
  // particularly because of the event/alter logic that we might want
  // to keep around or re-implement later for similar reasons (i.e.
  // allowing a given subscription to modify the recurring engine which
  // it is requesting during the checkout or renewal process. :)
  //
  // /**
  //  * {@inheritdoc}
  //  */
  // public function loadForUser(UserInterface $account) {
  //   $payment_gateways = $this->loadByProperties(['status' => TRUE]);
  //   $payment_gateways = array_filter($payment_gateways, function ($payment_gateway) {
  //     return $payment_gateway->getPlugin() instanceof SupportsStoredPaymentMethodsInterface;
  //   });
  //   // @todo Implement resolving logic.
  //   $payment_gateway = reset($payment_gateways);

  //   return $payment_gateway;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function loadMultipleForOrder(OrderInterface $order) {
  //   /** @var \Drupal\commerce_payment\Entity\RecurringEngineInterface[] $payment_gateways */
  //   $payment_gateways = $this->loadByProperties(['status' => TRUE]);
  //   // Allow the list of payment gateways to be filtered via code.
  //   $event = new FilterPaymentGatewaysEvent($payment_gateways, $order);
  //   $this->eventDispatcher->dispatch(PaymentEvents::FILTER_PAYMENT_GATEWAYS, $event);
  //   $payment_gateways = $event->getPaymentGateways();
  //   // Evaluate conditions for the remaining ones.
  //   foreach ($payment_gateways as $payment_gateway_id => $payment_gateway) {
  //     if (!$payment_gateway->applies($order)) {
  //       unset($payment_gateways[$payment_gateway_id]);
  //     }
  //   }
  //   uasort($payment_gateways, [$this->entityType->getClass(), 'sort']);

  //   return $payment_gateways;
  // }
}

