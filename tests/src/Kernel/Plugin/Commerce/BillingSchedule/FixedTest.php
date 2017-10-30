<?php

namespace Drupal\Tests\commerce_recurring\Plugin\Commerce\BillingSchedule;

use Drupal\commerce_recurring\Entity\BillingSchedule;
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
    BillingSchedule::create(['id' => 'test'])->save();

    // 1 hour
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'hour',
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
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
      '_entity_id' => 'test',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);

    $this->assertEquals('2017-01-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2019-01-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2019-01-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2021-01-01', $result2->getEndDate()->format('Y-m-d'));
  }

  public function testPrepaidMultipleCycles() {
    BillingSchedule::create(['id' => 'test', 'billing_type' => 'prepaid'])->save();
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'month',
      '_entity_id' => 'test',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);
    $result3 = $fixed->getNextBillingCycle($result2);
    $result4 = $fixed->getNextBillingCycle($result3);

    $this->assertEquals('2017-10-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-11-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-11-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-01', $result2->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-12-01', $result3->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-01-01', $result3->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2018-01-01', $result4->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-02-01', $result4->getEndDate()->format('Y-m-d'));
  }

  public function testPostpaidMultipleCycles() {
    BillingSchedule::create(['id' => 'test', 'billing_type' => 'postpaid'])->save();
    $fixed = new Fixed([
      'number' => 1,
      'unit' => 'month',
      '_entity_id' => 'test',
    ], '', []);

    $date = new DrupalDateTime('2017-10-09T15:07:12');
    $result = $fixed->getFirstBillingCycle($date);
    $result2 = $fixed->getNextBillingCycle($result);
    $result3 = $fixed->getNextBillingCycle($result2);
    $result4 = $fixed->getNextBillingCycle($result3);

    $this->assertEquals('2017-10-01', $result->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-11-01', $result->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-11-01', $result2->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2017-12-01', $result2->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2017-12-01', $result3->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-01-01', $result3->getEndDate()->format('Y-m-d'));

    $this->assertEquals('2018-01-01', $result4->getStartDate()->format('Y-m-d'));
    $this->assertEquals('2018-02-01', $result4->getEndDate()->format('Y-m-d'));
  }

}
