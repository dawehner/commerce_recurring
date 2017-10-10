<?php

namespace Drupal\commerce_recurring;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * List builder for subscriptions.
 */
class SubscriptionListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    // @todo Which columns to show ...
    $header = [
      'label' => [
        'data' => $this->t('Label'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'user' => [
        'data' => $this->t('User'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'state' => [
        'data' => $this->t('state'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_recurring\Entity\SubscriptionInterface */
    $row = [
      'label' => $entity->label(),
      'user' => $entity->getOwner()->getDisplayName(),
      'state' => $entity->getState()->getLabel(),
    ];

    return $row + parent::buildRow($entity);
  }

}
