<?php

namespace Drupal\Tests\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\Rolling;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the fixed billing schedule.
 *
 * @see \Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\Rolling
 * @group commerce_recurring
 */
class RollingTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring'];

  public function testRolling() {
    // 1 hour
    $fixed = new Rolling([
      'number' => 1,
      'unit' => 'hour',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:07:12', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T16:07:12', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-09T16:07:12', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T17:07:12', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 5 hour
    $fixed = new Rolling([
      'number' => 5,
      'unit' => 'hour',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:07:12', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T20:07:12', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-09T20:07:12', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-10T01:07:12', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 1 day
    $fixed = new Rolling([
      'number' => 1,
      'unit' => 'day',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:07:12', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-10T15:07:12', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-10T15:07:12', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-11T15:07:12', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 6 day
    $fixed = new Rolling([
      'number' => 6,
      'unit' => 'day',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:07:12', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-15T15:07:12', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-15T15:07:12', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-21T15:07:12', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 2 week
    $fixed = new Rolling([
      'number' => 2,
      'unit' => 'week',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-10-23', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-10-23', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-11-06', $result2->getEndDate()->format('Y-m-d'));

    // 1 month
    $fixed = new Rolling([
      'number' => 1,
      'unit' => 'month',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-11-09', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-11-09', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-09', $result2->getEndDate()->format('Y-m-d'));

    // 2 month
    $fixed = new Rolling([
      'number' => 2,
      'unit' => 'month',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-09', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-12-09', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-02-09', $result2->getEndDate()->format('Y-m-d'));

    // 2 year
    $fixed = new Rolling([
      'number' => 2,
      'unit' => 'year',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2019-10-09', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2019-10-09', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2021-10-09', $result2->getEndDate()->format('Y-m-d'));
  }

  public function testPrepaidMultipleCycles() {
    $fixed = new Rolling([
      'number' => 1,
      'unit' => 'month',
      'billing_type' => 'prepaid',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);
    $result3 = $fixed->getNextBillingCycle($result2);
    $result4 = $fixed->getNextBillingCycle($result3);

    $this->assertEquals('2017-11-09', $result->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2017-12-09', $result->getEndDateTime()->format('Y-m-d'));

    $this->assertEquals('2017-12-09', $result2->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2018-01-09', $result2->getEndDateTime()->format('Y-m-d'));

    $this->assertEquals('2018-01-09', $result3->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2018-02-09', $result3->getEndDateTime()->format('Y-m-d'));

    $this->assertEquals('2018-02-09', $result4->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2018-03-09', $result4->getEndDateTime()->format('Y-m-d'));
  }

  public function testPostpaidMultipleCycles() {
    $fixed = new Rolling([
      'number' => 1,
      'unit' => 'month',
      'billing_type' => 'postpaid',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);
    $result3 = $fixed->getNextBillingCycle($result2);
    $result4 = $fixed->getNextBillingCycle($result3);

    $this->assertEquals('2017-10-09', $result->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2017-11-09', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-11-09', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-09', $result2->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-12-09', $result3->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2018-01-09', $result3->getEndDateTime()->format('Y-m-d'));

    $this->assertEquals('2018-01-09', $result4->getStartDateTime()->format('Y-m-d'));
    $this->assertEquals('2018-02-09', $result4->getEndDateTime()->format('Y-m-d'));
  }

}
