<?php

namespace Drupal\commerce_recurring;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the list builder for recurring engines.
 */
class RecurringEngineListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'engines';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_recurring_engines';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Recurring engine');
    // @TODO: Figure out if there are any plugin-type-wide properties
    // that we really want to include in this table.
    // $header['mode'] = $this->t('Mode');
    // $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_recurring\Entity\RecurringEngineInterface $entity */
    $engine_plugin = $entity->getPlugin();
    // @TODO: Is there a use case for disabling a recurring engine config?
    // $status = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    $row['label'] = $entity->label();
    // $this->weightKey determines whether the table will be rendered as a form.
    // @TODO: Preserving this boilerplate because this seems important if
    // we do add other non-label properties? And why don't we need to do
    // this with labels?
    // if (!empty($this->weightKey)) {
    //   $row['mode']['#markup'] = $mode;
    //   $row['status']['#markup'] = $status;
    // }
    // else {
    //   $row['mode'] = $mode;
    //   $row['status'] = $status;
    // }

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $entities = $this->load();
    // If there are less than 2 engines, disable dragging.
    if (count($entities) <= 1) {
      unset($this->weightKey);
    }
    return parent::render();
  }

}

