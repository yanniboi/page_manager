<?php

/**
 * @file
 * Contains \Drupal\block_page\Tests\BlockPageAdminTest.
 */

namespace Drupal\block_page\Tests;

use Drupal\Component\Utility\String;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the admin UI for block pages.
 */
class BlockPageAdminTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('block_page');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Block Page admin test',
      'description' => 'Tests the admin UI for block pages.',
      'group' => 'Block Page',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalLogin($this->drupalCreateUser(array('administer block pages')));
  }

  public function testAdmin() {
    $this->drupalGet('admin/structure/block_page');
    $this->assertText('There is no Block Page yet.');

    // Add a new block page.
    $this->clickLink('Add block page');
    $edit = array(
      'label' => 'Foo',
      'id' => 'foo',
      'path' => 'admin/foo',
    );
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertRaw(String::format('The %label block page has been added.', array('%label' => 'Foo')));

    // Test that it is available immediately.
    $this->drupalGet('admin/foo');
    $this->assertTitle('Foo | Drupal');

    // Add a new page variant.
    $this->drupalGet('admin/structure/block_page/manage/foo');
    $this->assertText('There are no page variants.');
    $this->clickLink('Add new page variant');
    $edit = array(
      'plugin[label]' => 'First',
    );
    $this->drupalPostForm(NULL, $edit, 'Add page variant');
    $this->assertRaw(String::format('The %label page variant has been added.', array('%label' => 'First')));

    // Add a block to the variant.
    $this->clickLink('User account menu');
    $edit = array(
      'region' => 'top',
    );
    $this->drupalPostForm(NULL, $edit, 'Add block');

    // Test that the block is displayed.
    $this->drupalGet('admin/foo');
    $elements = $this->xpath('//div[@class="block-region-top"]/div/ul[@class="menu"]/li/a');
    $expected = array('My account', 'Log out');
    $links = array();
    foreach ($elements as $element) {
      $links[] = (string) $element;
    }
    $this->assertEqual($expected, $links);
  }

}
