<?php

namespace Drupal\commerce_recurring\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class SubscriptionForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    drupal_set_message($this->t('A subscription been successfully saved.'));
    $form_state->setRedirect('entity.commerce_subscription.collection');
  }

}
