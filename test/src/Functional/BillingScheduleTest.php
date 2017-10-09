<?php

namespace Drupal\Tests\commerce_recurring\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests billing schedules
 */
class BillingScheduleTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['commerce_recurring', 'commerce_recurring_test', 'block'];

  public function testCrudUiTest() {
    $admin_user = $this->drupalCreateUser(['administer commerce_billing_schedules']);
    $this->drupalLogin($admin_user);
    $this->placeBlock('local_actions_block');

    // 1. Create an entity
    $this->drupalGet('admin/commerce/config/billing-schedule');
    $this->assertSession()->statusCodeEquals(200);

    $this->clickLink('Add billing schedule');
    $this->submitForm([
      'label' => 'My admin label',
      'id' => 'test_id',
      'display_label' => 'My display label',
      'plugin' => 'test_plugin',
    ], 'Save');
    $this->clickLink('Edit');
    $this->submitForm([
      'configuration[key]' => 'value1',
    ], 'Save');
    $this->assertSession()->addressEquals('admin/commerce/config/billing-schedule');
    $this->assertSession()->pageTextContains('Billing schedule My admin label created');

    // 2. Ensure the entity is listed
    $this->assertSession()->pageTextContains('test_id');

    // 3. Edit the entity
    $this->clickLink('Edit');
    $this->assertSession()->fieldValueEquals('configuration[key]', 'value1');
    $this->submitForm([
      'configuration[key]' => 'value2',
    ], 'Save');
    $this->assertSession()->addressEquals('admin/commerce/config/billing-schedule');
    $this->clickLink('Edit');
    $this->assertSession()->fieldValueEquals('configuration[key]', 'value2');
    $this->submitForm([], 'Save');

    // 4. Delete the entity
    $this->clickLink('Delete');
    $this->submitForm([], 'Delete');

    $this->assertSession()->pageTextNotContains('test_id');
  }

}
