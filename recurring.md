## Commerce Recurring 2.x

### Intro

Recurring billing is an extremely complex feature with many use cases. In order to support all of these use cases in a robust way, a fully-featured Recurring module for Drupal Commerce 2.x needs to support them all, and be properly configurable/pluggable in a way that allows sufficient customization for developers while also being usable for store administrators.

This is a specification that attempts to transfer our accumulated knowledge from years of working on Commerce License Billing 1.x while also taking advantage of the designs Bojan and I worked on with Actualys and the Platform.sh team back in January.

### The Overall Picture

The recurring module uses a series of linked entities and plugins to drive a recurring billing process. These are:

`Recurring Engine`: A plugin which provides logic to determine how to generate successive billing cycles. Acts as a bundle for `Recurring Cycle Types`. We also envision that the engine will be used to transport orders to a remote system (such as Paypal or Stripe managed subscription plans) rather than using the default card-on-file charge methods.

`Recurring Cycle Type`: A config entity which provides the necessary config for a `Recurring Engine` (which acts as its bundle plugin). Invokes the proper engine plugin methods to create new or repeated `Recurring Cycles` for a given customer.

`Recurring Cycle`: A "content" entity which governs the recurring workflow for an order as well as recording the time period to which it applies. Uses its bundle's plugin logic to create a new billing cycle and recurring order when it is renewed, and uses the same plugin logic to move an order into payment when it is renewed. 

`Recurring Order`: A new bundle of order which has a completely separate workflow (partially managed by billing cycles) from cart orders. Recurring orders generate order items based on their attached `Subscriptions` (see below) and are normally paid via card-on-file charges once they reach the appropriate workflow stage.

`Recurring Order Item`: A new bundle of order item which is used in recurring orders. Each recurring line item is generated by a `Charge Object` which are in turn generated by a `Subscription` and its plugin-specific methods for charge collection.

`Subscription Type`: A plugin which provides the logic that a `Subscription` needs to function. Primarily this logic is around knowing when to create themselves during a normal (cart or admin) order workflow, typically triggered by a product being checked out which has a trait field containing the plugin config for the `Subscription Type`. Also, the type plugins provide the logic which refers to the generating entity (product, order, bundle, license, etc.) and generates a set of `Charge Objects` which are then used to create the recurring order's items. Part of the default plugin config is always a reference to a `Recurring Cycle Type` which is how a subscription determines to which billing cycle and order pair to attach itself.

`Subscription`: The actual entity generated by a subscribable entity when it is checked out. The subscription contains both its plugin configuration (for its `Subscription Type` bundle plugin) as well as references to its generating data or entity. When recurring orders are refreshed, all attached `Subscriptions` collect their charges (based on the underlying entities or data.) These `Charge Objects` are stateless representations of the base charges that a `Subscription` desires for a specific billing cycle.

`Usage Group`: A plugin which uses type-specific logic to record usage information and generate charges for a subscription. Subscriptions implementing the UsageInterface can register one or more usage groups (ideally via the UI although we haven't contemplated this in 1.x yet) and they will generate appropriate `Charge Objects` for the recurring order.

`Usage Record`: A value entity generated when usage is added to a usage group. Various usage groups use the records in different ways, and then from them compute the necessary `Charge Objects` to add to the `Subscription` at order refresh time.

`Charge Object`: A value object which is generated by a subscription based on its plugin/bundle logic. For a product bundle, this means generating a charge for each product in the bundle. For any subscription using usage records, this would be all usage charges generated by each usage group. For subscriptions with varying states (i.e. the ability to suspend a subscription temporarily) this might involve charges split up along those timeframes.

`Matching Service`: We plan to use a set of tagged services to match `Charge Objects` (which are stateless containers for a base charge) to existing `Order Items` with the goal of refreshing an order and _not_ re-saving the order entity or order items if they have not been changed. However, different types of charge objects might request non-default matching schemes, so we allow them to do this via a service plugin (since they are normally just value objects and not linked to any specific logic.)

`Order Item Generator Service`: Similarly, we need logic that generates the actual order item from a `Charge Object` if there is no existing order item to be found. We also allow other services to be used here in case an override is desired.

This gives us the following overall workflow:

1. Various entities are considered "subscribable", and configure a subscription type as part of an entity trait field on the product or order. (One major outstanding architectural question is if there is a unified way of doing this that lets us handle entire-orders, product bundles, individual products, and licenses-from-products gracefully.)

2. When an order reaches a completed workflow state (exact states TBD), it reviews itself and its order items for the presence of subscribable entities.

3. Based on the results from #2, a `Subscription` is generated and (using its own reference to a `Recurring Cycle Type`) it is assigned to a `Recurring Cycle` and `Recurring Order` pair. (This logic is smart enough to generate and save appropriate cycle and order entities if none are present for the desired user and timestamp.

4. Once attached to an order (presumably via a reverse reference field on the order) the `Subscription` can then modify the order refresh process. It generates the appropriate charges for the order's `Recurring Cycle`,

5. The attached charges are turned into `Order Items` appropriately and the `Recurring Order` is saved if any changes have been made since the last refresh.

6. On cron, billing cycles are evaluated and any whose end has passed have their `->close()` and `->renew()` operations queued up for processing.

7. This has the effect of spawning a new billing cycle and order (renewing the cycle) which contains the same set of subscriptions and also charging the order as its workflow state is advanced (closing the cycle) to a payment requested state of some kind.

```php
/**
 * Entity bundle plugin for billing cycle types.
 */
interface RecurringEngine {
  /**
   * @param Account $account
   * @param DateTime $startTime
   * @return RecurringCycle $cycle
   */
  getRecurringCycle(Account $account, DateTime(?) $startTime)

  /**
   * @param RecurringCycle $cycle
   * @return RecurringCycle $newCycle
   */
  getNextRecurringCycle(RecurringCycle $cycle)

  /**
   * @param RecurringCycle $cycle
   * @return ??? $status
   * Renew the cycle. Base implementation mimicks the 1.x version:
   *   - Change the cycle workflow (?) to renewed (and bail if renewal has
   *   already taken place? Unclear.)
   *   - Get all subscriptions from the order attached to the cycle
   *   - Run all scheduled changes on each subscription, if any
   *   - Renew all subscriptions
   *   - For each subscription, get the billing cycle type and next cycle
   *   - For each cycle type + license list, create a new recurring order
   * Non-standard implementations of this are possible and at-your-own-risk.
   */
  renewCycle(RecurringCycle $cycle)

  /**
   * @param RecurringCycle $cycle
   * @return ??? $status
   * Close the cycle. Base implementation mimicks the 1.x version:
   *   - Change the cycle workflow (?) to closed (and bail if it is already
   *   closed? Unclear.)
   *   - Get all the subscriptions from the order.
   *   - For each subscription, check if it can be charged
   *   - Normally this is about usage groups but other implementations are
   *   possible
   *   - Move the order workflow to payment pending (?) if possible
   *   - Do any requested cleanup from ... the subscription/usage groups/billing
   *   cycle type? Gotta think about this.
   *   - Otherwise change order workflow to completion_pending? (Formerly
   *   usage_pending...)
   */
  closeCycle(RecurringCycle $cycle)

  /**
   * @param RecurringOrder $order
   * @return ??? $status
   * Refreshes an order.
   *
   * @TODO: Assuming we stick with this existing on the recurring engine
   * plugin, we'll want our custom order refresher to phone home to this.
   *
   */
  refreshOrder(Order $order)

  /**
   * @param Order $previousOrder
   * @param RecurringCycle $cycle
   * @param Subscription[] $subscriptions
   * @return RecurringOrder $newOrder
   * Generate a recurring order for a set of subscriptions.
   *   - If an order already exists for the billing cycle, it will be used
   *   - Otherwise a new order is generated
   *   - A defined set of values (especially customer profiles) are copied to
   *   the new order
   *   - Add the requested subscriptions to the attachment subscription
   *   reference array on the order
   */
  createRecurringOrder($previousOrder = NULL, )

  /**
   * @TODO: Figure out how the billing engine needs to be involved in order 
   * item creation and pricing, if at all. I feel like for the periodic plugin
   * type to work we should not be hard-coding any of its time-based assumptions
   * but I'm not sure of the code layout that will give us what we want here.
   *
   * Stay tuned.
   */ 
}

/**
 * Defines the recurring cycle type entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_recurring_cycle_type",
 *   label = @Translation("Recurring cycle type"),
 *   label_collection = @Translation("Recurring cycle types"),
 *   label_singular = @Translation("recurring cycle type"),
 *   label_plural = @Translation("recurring cycle types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count recurring cycle type",
 *     plural = "@count recurring cycle types",
 *   ),
 *   bundle_label = @Translation("Recurring engine"),
 *   bundle_plugin_type = "commerce_recurring_engine",
 *   handlers = {
 *     "access" = "Drupal\commerce_recurring\RecurringCycleTypeAccessControlHandler",
 *     "list_builder" = "Drupal\commerce_recurring\RecurringCycleTypeListBuilder",
 *     "storage" = "Drupal\commerce_recurring\RecurringCycleTypeStorage",
 *     "form" = {
 *       "edit" = "Drupal\commerce_recurring\Form\RecurringCycleTypeEditForm",
 *       "delete" = "Drupal\commerce_recurring\Form\RecurringCycleTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_recurring_cycle_type",
 *   admin_permission = "administer commerce_recurring_cycle_type",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "recurring_cycle_type_id",
 *     "name" = "machine_name", // ?
 *     "uuid" = "uuid",
 *     "bundle" = "engine"
 *   },
 *   links = {
 *     "collection" = "/admin/commerce/config/recurring-cycle-types",
 *     "canonical" = "/admin/commerce/config/recurring-cycle-types/{recurring_cycle_type}/edit",
 *     "edit-form" = "/admin/commerce/config/recurring-cycle-types/{recurring_cycle_type}/edit",
 *     "delete-form" = "/admin/commerce/config/recurring-cycle-types/{recurring_cycle_type}/delete",
 *   },
 * )
 *
 * TBD if using a content entity as the bundle for another content entity is
 * going to blow up in my face. Stay tuned.
 *   
 */
class RecurringCycleType extends ContentEntityBase implements RecurringCycleTypeInterface {
  // No need for fields, but we need a trait to fuel the config for the engine
  // plugin that serves as the bundle.

}

/**
 * Defines the recurring cycle entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_recurring_cycle",
 *   label = @Translation("Recurring cycle"),
 *   label_collection = @Translation("Recurring cycles"),
 *   label_singular = @Translation("recurring cycle"),
 *   label_plural = @Translation("recurring cycles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count recurring cycle type",
 *     plural = "@count recurring cycle types",
 *   ),
 *   bundle_label = @Translation("Recurring cycle type"),
 *   bundle_entity_type = "commerce_recurring_cycle_type",
 *   handlers = {
 *     "access" = "Drupal\commerce_recurring\RecurringCycleAccessControlHandler",
 *     "list_builder" = "Drupal\commerce_recurring\RecurringCycleListBuilder",
 *     "storage" = "Drupal\commerce_recurring\RecurringCycleStorage",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_recurring_cycle",
 *   admin_permission = "administer commerce_recurring_cycle",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "recurring_cycle_id",
 *     "uuid" = "uuid",
 *     "bundle" = "type"
 *   },
 *   links = {
 *     "collection" = "/admin/commerce/config/recurring-cycles",
 *     "canonical" = "/admin/commerce/config/recurring-cycles/{recurring_cycle}",
 *   },
 * )
 *
 * Recurring cycles don't have a UI -- they are created by various
 * recurring/subscription processes and then deleted if their order is deleted
 * by some other method. @TODO is we could provide a workflow-only UI to allow
 * people to manually close/renew cycles early or late but it is not clear if
 * this can be made to work with even the default periodic engine
 * implementation. 
 *
 */
class RecurringCycle extends ContentEntityBase implements RecurringCycleInterface {
  public function baseFieldDefinitions() {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Owner'))
      ->setDescription(t('The user ID of the license owner.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_recurring\Entity\RecurringCycle::getCurrentUserId')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['state'] = BaseFieldDefinition::create('state')
      ->setLabel(t('State'))
      ->setDescription(t('The recurring cycle state.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'state_transition_form',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setSetting('workflow_callback', ['\Drupal\commerce_recurring\Entity\RecurringCycle', 'getWorkflowId']);
    
    // @TODO Unclear how to generalize this if we rely on the RecurringEngine
    // plugin model.
    $fields['start'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Start'))
      ->setDescription(t('The start date of the recurring cycle.'))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 1,
        'settings' => [
          'date_format' => 'custom',
          'custom_date_format' => 'n/Y',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(0);
   
    // @TODO Unclear how to generalize this if we rely on the RecurringEngine
    // plugin model.
    $fields['end'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('End'))
      ->setDescription(t('The end date of the recurring cycle.'))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 1,
        'settings' => [
          'date_format' => 'custom',
          'custom_date_format' => 'n/Y',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(0);

  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }
}

// @TODO: Usage groups

// Recurring order type. config/install/commerce_order.commerce_order_type.recurring.yml
// @TODO: Where do we attach a different OrderRefresh object to this?
// @TODO: Attach subscription references field to this

langcode: en
status: true
label: Recurring
id: recurring
workflow: order_recurring
traits: {  }
refresh_mode: recurring
refresh_frequency: 300
sendReceipt: true
receiptBcc: ''

// config/install/field.field.commerce_order.recurring.order_subscriptions.yml

langcode: en
status: true
dependencies:
  config:
    - commerce_order.commerce_order_type.recurring
    - field.storage.commerce_order.order_subscriptions
id: commerce_order.recurring.order_subscriptions
field_name: order_subscriptions
entity_type: commerce_order
bundle: recurring
label: 'Subscriptions'
description: ''
required: true
translatable: false
default_value: {  }
default_value_callback: ''
settings:
  handler: 'default:commerce_subscription'
  handler_settings: {  }
field_type: entity_reference


// config/install/field.storage.commerce_order.order_subscriptions.yml

langcode: en
status: true
dependencies:
  module:
    - commerce_order
id: commerce_order.order_subscriptions
field_name: order_subscriptions
entity_type: commerce_subscription
type: entity_reference
settings:
  target_type: commerce_subscription
module: core
locked: true
cardinality: -1
translatable: false
indexes: {  }
persist_with_no_fields: false
custom_storage: false

// Recurring order item type. config/install/commerce_order.commerce_order_item_type.recurring.yml

langcode: en
status: true
dependencies:
  enforced:
    module:
      - commerce_recurring
label: 'Recurring'
id: recurring
purchasableEntityType: commerce_subscription
orderType: recurring
// @TODO: We might want a plugin field for usage groups here?
traits: {  }
// @TODO: We might also want a way to record the charge object
// responsible for this order item. It has come up before...


/**
 * Defines the subscription entity.
 *
 * @ingroup commerce_recurring
 *
 * @ContentEntityType(
 *   id = "commerce_subscription",
 *   label = @Translation("Subscription"),
 *   label_collection = @Translation("Subscriptions"),
 *   label_singular = @Translation("subscription"),
 *   label_plural = @Translation("subscriptions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count subscription",
 *     plural = "@count subscription",
 *   ),
 *   bundle_label = @Translation("Subscription type"),
 *   bundle_plugin_type = "commerce_subscription_type",
 *   handlers = {
 *     "access" = "Drupal\commerce_recurring\SubscriptionAccessControlHandler",
 *     "list_builder" = "Drupal\commerce_recurring\SubscriptionListBuilder",
 *     "storage" = "Drupal\commerce_recurring\SubscriptionStorage",
 *     "form" = {
 *       "default" = "Drupal\commerce_recurring\Form\SubscriptionForm",
 *       "checkout" = "Drupal\commerce_recurring\Form\SubscriptionCheckoutForm",
 *       "edit" = "Drupal\commerce_recurring\Form\SubscriptionForm",
 *       "delete" = "Drupal\commerce_recurring\Form\SubscriptionDeleteForm",
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_subscription,
 *   admin_permission = "administer subscriptions",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "subscription_id",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/subscriptions/{commerce_subscription}",
 *     "edit-form" = "/admin/commerce/subscriptions/{commerce_subscription}/edit",
 *     "delete-form" = "/admin/commerce/subscriptions/{commerce_subscription}/delete",
 *     "collection" = "/admin/commerce/subscriptions",
 *   },
 * )
 */
 *
 */
class Subscription extends ContentEntity {

}

/**
 * Defines the interface for subscription types.
 */
interface SubscriptionTypeInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Gets the subscription type label.
   *
   * @return string
   *   The subscription type label.
   */
  public function getLabel();

  /**
   * Build a label for the given subscription type.
   *
   * @param \Drupal\commerce_recurring\Entity\SubscriptionInterface $license
   *
   * @return string
   *   The label.
   */
  public function buildLabel(SubscriptionInterface $subscription);

  /**
   * Gets the workflow ID this this subscription type should use.
   *
   * @return string
   *   The ID of the workflow used for this subscription type.
   */
  public function getWorkflowId();

  /**
   * Generate the charges for this subscription and a given recurring cycle.
   */
  public function collectCharges(RecurringCycle $cycle) {
    // Default implementation here varies a lot:
    // Does the subscription represent:
    //   a. A single product or bundle?
    //   b. An entire cart / repeated order of some kind?
    //   c. A license which has recurring billing configured?
    //   d. Any one of these things, plus usage groups?

    // The answer to this question is one of the key parts of the
    // subscription type plugin and determines the implementation.
  }

  /**
   * Check whether plan changes can be made to this subscription during
   * the middle of a recurring cycle.
   */
  public function enforceChangeScheduling(RecurringCycle $cycle) {

  }
}

/**
 * Usage group plugin type.
 */
interface RecurringUsageGroup {
  /**
   * Determines whether this usage group plugin should block the subscription's plan from being changed midstream.
   */
  public function enforceChangeScheduling(RecurringCycle $cycle) {

  }

  /**
   * Returns a list of usage records for a given recurring cycle.
   */
  public function usageHistory(RecurringCycle $cycle);

  /**
   * Adds usage for this usage group and subscription and
   * recurring cycle.
   */
  public function addUsage(mixed $usage, RecurringCycle $cycle);

  /**
   * Gets the current usage (normally an integer, but who knows)
   * for this usage group.
   */
  public function currentUsage();

  /**
   * Checks whether usage records are complete for a given recurring
   * cycle or whether the subscription needs to "wait" on remote
   * services that might record usage data into the system later.
   */
  public function isComplete();

  /**
   * Returns the charges for this group and a given recurring cycle.
   */
  public function getCharges(RecurringCycle $cycle);

  /**
   * We need something to react to changes in the subscription plan.
   * In 1.x this was "onRevisionChange" but that might not make sense anymore.
   */
  public function onPlanChange();
}

// @TODO: Charge value object class definition.
// @TODO: Service to transform charge objects into order items.


```

