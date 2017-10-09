<?php

namespace Drupal\commerce_recurring;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * List builder for billing schedules.
 */
class BillingScheduleListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'label' => [
        'data' => $this->t('Admin label'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'type' => [
        'data' => $this->t('Type'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'id' => [
        'data' => $this->t('Id'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];

    return $header + parent::buildHeader();
  }

  
  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_recurring\Entity\BillingScheduleInterface */
    $row = [
      'label' => $entity->label(),
      'type' => $entity->getPlugin()->getPluginDefinition()['label'],
      'id' => $entity->id(),
    ];

    return $row + parent::buildRow($entity);
  }

}
