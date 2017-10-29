<?php

namespace Drupal\Tests\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\Fixed;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the fixed billing schedule.
 *
 * @see \Drupal\commerce_recurring\Plugin\Commerce\BillingSchedule\Fixed
 * @group commerce_recurring
 */
class FixedTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring'];

  public function testFixed() {
    // 1 hour
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'hour',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T16:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-09T16:00:00', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T17:00:00', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 5 hour
    $fixed = new Fixed([
      'number' => 5,
      'unit' => 'hour',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T15:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-09T20:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-09T20:00:00', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-10T01:00:00', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 1 day
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'day',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T00:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-10T00:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-10T00:00:00', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-11T00:00:00', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 6 day
    $fixed = new Fixed([
      'number' => 6,
      'unit' => 'day',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-09T00:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-15T00:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));

    $this->assertEquals('2017-10-15T00:00:00', $result2->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-10-21T00:00:00', $result2->getEndDate()->format('Y-m-d\TH:i:s'));

    // 2 week
    $fixed = new Fixed([
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
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'month',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-11-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-11-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-01', $result2->getEndDate()->format('Y-m-d'));

    // 2 month
    $fixed = new Fixed([
      'number' => 2,
      'unit' => 'month',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-10-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-12-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-02-01', $result2->getEndDate()->format('Y-m-d'));

    // 2 year
    $fixed = new Fixed([
      'number' => 2,
      'unit' => 'year',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-01-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2019-01-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2019-01-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2021-01-01', $result2->getEndDate()->format('Y-m-d'));
  }

}
