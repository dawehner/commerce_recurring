<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_recurring\BillingCycle;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the billing cycle field.
 *
 * @group commerce_recurring
 */
class BillingCycleFieldTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring', 'field', 'entity_test', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');
  }

  public function testFieldItem() {
    FieldStorageConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_billing_cycle',
      'type' => 'commerce_billing_cycle',
    ])->save();

    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_billing_cycle',
      'bundle' => 'entity_test',
    ])->save();

    $entity = EntityTest::create([
      'field_billing_cycle' => [
        'start_date' => '2017-10-09T15:07:12',
        'end_date' => '2017-11-09T15:07:12',
      ],
    ]);
    $entity->save();

    $entity = EntityTest::load($entity->id());

    /** @var \Drupal\commerce_recurring\BillingCycle $billing_cycle */
    $billing_cycle = $entity->get('field_billing_cycle')->get(0)->toBillingCycle();
    $this->assertInstanceOf(BillingCycle::class, $billing_cycle);
    $this->assertInstanceOf(DrupalDateTime::class, $billing_cycle->getStartDate());
    $this->assertInstanceOf(DrupalDateTime::class, $billing_cycle->getEndDate());
    $this->assertEquals('2017-10-09T15:07:12', $billing_cycle->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2017-11-09T15:07:12', $billing_cycle->getEndDate()->format('Y-m-d\TH:i:s'));

    // Make it possible to set date time objects
    $entity->get('field_billing_cycle')->get(0)->setValue([
      'start_date' => new \DateTime('2018-10-09T15:07:12'),
      'end_date' => new \DateTime('2019-10-09T15:07:12'),
    ]);

    $billing_cycle = $entity->get('field_billing_cycle')->get(0)->toBillingCycle();
    $this->assertInstanceOf(BillingCycle::class, $billing_cycle);
    $this->assertEquals('2018-10-09T15:07:12', $billing_cycle->getStartDate()->format('Y-m-d\TH:i:s'));
    $this->assertEquals('2019-10-09T15:07:12', $billing_cycle->getEndDate()->format('Y-m-d\TH:i:s'));
  }
}
