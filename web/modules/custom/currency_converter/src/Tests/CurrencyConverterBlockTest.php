<?php

namespace Drupal\currency_converter\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests if Currency converter block is available.
 *
 * @group currency_converter
 */
class CurrencyConverterBlockTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system_test',
    'block',
    'currency_converter'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create and login user.
    $admin_user = $this->drupalCreateUser(array(
      'administer blocks',
      'administer site configuration',
      'access administration pages',
    ));
    $this->drupalLogin($admin_user);
  }

  /**
   * Test that the currency converter block can be placed and works.
   */
  public function testCurrencyConverterBlock() {
    // Test availability of the twitter block in the admin "Place blocks" list.
    \Drupal::service('theme_handler')->install(['bartik', 'seven', 'stark']);
    $theme_settings = $this->config('system.theme');
    foreach (['bartik', 'seven', 'stark'] as $theme) {
      $this->drupalGet('admin/structure/block/list/' . $theme);
      $this->assertResponse(200);
      // Configure and save the block.
      $this->drupalPlaceBlock('currency_converter_block', array(
        'currency_converter_from' => 'AED',
        'currency_converter_to' => 'AFN',
        'region' => 'content',
        'theme' => $theme,
      ));
      // Set the default theme and ensure the block is placed.
      $theme_settings->set('default', $theme)->save();
      $this->drupalGet('');
      $this->assertText('Select Your Currency From', 'Currency Converter block found');
      $edit = [];
      $edit['currency_converter_from'] = 'AED';
      $edit['currency_converter_to'] = 'AFN';
      $edit['amount'] = 100;
      $this->drupalPostForm('', $edit, t('Convert'));
      $result = \Drupal::service('currency_converter.manager')
        ->convertAmount(100, 'AED', 'AFN');
      $output = t('Your selected value is from AED to AFN amount is 100 AED &amp; your converted value is @result AFN', array('@result' => $result));
      $this->assertText($output);

      // Test error message same currency.
      $edit['currency_converter_from'] = 'AED';
      $edit['currency_converter_to'] = 'AED';
      $this->drupalPostForm('', $edit, t('Convert'));
      $this->assertText(t('Please select different currency both currency are same.'));
    }
  }

}
