<?php

namespace Drupal\commerce_recurring;

use Drupal\commerce_price\Price;

class Charge {

  /**
   * The amount of the charge.
   *
   * @var \Drupal\commerce_price\Price
   */
  protected $amount;

  /**
   * Charge constructor.
   *
   * @param \Drupal\commerce_price\Price $amount
   *   The amount of the charge.
   */
  public function __construct(Price $amount) {
    $this->amount = $amount;
  }

  /**
   * @return \Drupal\commerce_price\Price
   */
  public function getAmount() {
    return $this->amount;
  }

}
