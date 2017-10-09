<?php

namespace Drupal\Tests\commerce_recurring\Kernel;

use Drupal\commerce_recurring\Entity\BillingSchedule;
use Drupal\commerce_recurring_test\Plugin\Commerce\BillingSchedule\TestPlugin;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests billing schedules.
 *
 * @group commerce_recurring
 */
class BillingScheduleTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring', 'commerce_recurring_test'];

  public function testCreateWithNotExistingPlugin() {
    $this->setExpectedException(\Exception::class);
    BillingSchedule::create([
      'id' => 'test_id',
      'label' => 'Test label',
      'plugin' => 'not_existing',
      'configuration' => [
        'key' => 'value',
      ],
    ])->save();
  }

  public function testCrudEntity() {
    BillingSchedule::create([
      'id' => 'test_id',
      'label' => 'Test label',
      'display_label' => 'Test customer label',
      'plugin' => 'test_plugin',
      'configuration' => [
        'key' => 'value',
      ],
    ])
      ->save();

    $billing_schedule = BillingSchedule::load('test_id');
    $this->assertEquals('test_id', $billing_schedule->id());
    $this->assertEquals('Test label', $billing_schedule->label());
    $this->assertEquals('test_plugin', $billing_schedule->getPluginId());
    $this->assertEquals('Test customer label', $billing_schedule->getDisplayLabel());
  }

  public function testPluginConfiguration() {
    BillingSchedule::create([
      'id' => 'test_id',
      'label' => 'Test label',
      'plugin' => 'test_plugin',
      'configuration' => [
        'key' => 'value',
      ],
    ])
      ->save();

    /** @var \Drupal\commerce_recurring\Entity\BillingScheduleInterface $billing_schedule */
    $billing_schedule = BillingSchedule::load('test_id');

    $billing_schedule->setPluginConfiguration(['key' => 'value2']);
    $this->assertEquals('value2', $billing_schedule->getPluginConfiguration()['key']);
    $this->assertInstanceOf(TestPlugin::class, $billing_schedule->getPlugin());
  }

}
