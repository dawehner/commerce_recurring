<?php

namespace Drupal\commerce_recurring\EventSubscriber;

use Drupal\commerce\Event\CommerceEvents;
use Drupal\commerce\Event\ReferenceablePluginTypesEvent;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PluginItemSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function onPluginTypes(ReferenceablePluginTypesEvent $event) {
    $types = $event->getPluginTypes();
    $types['commerce_subscription_type'] = $this->t('Commerce subscription type');
    $event->setPluginTypes($types);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommerceEvents::REFERENCEABLE_PLUGIN_TYPES][] = ['onPluginTypes'];
    return $events;
  }


}
