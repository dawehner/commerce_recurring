<?php

namespace Drupal\commerce_recurring\SubscriptionChange;

use Drupal\commerce_price\Price;
use Drupal\commerce_recurring\Entity\SubscriptionInterface;

class SubscriptionChangeManager implements SubscriptionChangeManagerInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * ScheduleChangeManager constructor.
   */
  public function __construct() {
    $this->database = \Drupal::database();
    $this->time = \Drupal::time();
  }

  public function addScheduledChange(SubscriptionChange $subscription_change) {
    $insert = $this->database->insert('commerce_subscription_change');
    $value = $subscription_change->getValue();

    // @todo Should we generalize serialization handling?
    if ($value instanceof Price) {
      $value = serialize($value);
    }

    $insert->fields([
      'subscription_id' => $subscription_change->getSubscriptionId(),
      'field_name' => $subscription_change->getFieldName(),
      'value' => $value,
      'created' => $subscription_change->getCreated() ?: $this->time->getRequestTime(),
    ]);
    $insert->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getChangesPerSubscription(SubscriptionInterface $subscription) {
    $query = $this->database->select('commerce_subscription_change');
    $query->condition('subscription_id', $subscription->id());
    $query->fields('commerce_subscription_change');
    $query->orderBy('created', 'DESC');
    $changes = $query->execute()->fetchAll();

    return array_map(function ($change) use ($subscription) {
      $value = $change->value;
      if ($change->field_name === 'amount') {
        $value = unserialize($value);
      }
      return new SubscriptionChange($subscription->id(), $change->field_name, $value, $change->created);
    }, $changes);
  }

  /**
   * {@inheritdoc}
   */
  public function applyChanges(SubscriptionInterface $subscription, array $subscription_changes) {
    /** @var \Drupal\commerce_recurring\SubscriptionChange\SubscriptionChange $subscription_change */
    foreach ($subscription_changes as $subscription_change) {
      if ($subscription_change->getSubscriptionId() != $subscription->id()) {
        // @todo Should we do some error handling in this case?
        continue;
      }

      $subscription->set($subscription_change->getFieldName(), $subscription_change->getValue());
    }
    $subscription->save();
    return $subscription;
  }

}
