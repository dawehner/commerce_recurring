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
        'data' => $this->t('Label'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'type' => [
        'data' => $this->t('Type'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
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
    ];

    return $row + parent::buildRow($entity);
  }

}
