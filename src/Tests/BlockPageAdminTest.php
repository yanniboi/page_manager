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
    $this->doTestAddBlockPage();
    $this->doTestAddPageVariant();
    $this->doTestAddBlock();
    $this->doTestEditPageVariant();
  }

  /**
   * Tests adding a block page.
   */
  protected function doTestAddBlockPage() {
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

    // Assert that a page variant was added by default.
    $this->drupalGet('admin/structure/block_page/manage/foo');
    $this->assertNoText('There are no page variants.');
  }

  /**
   * Tests adding a page variant.
   */
  protected function doTestAddPageVariant() {
    // Add a new page variant.
    $this->clickLink('Add new page variant');
    $edit = array(
      'plugin[label]' => 'First',
    );
    $this->drupalPostForm(NULL, $edit, 'Add page variant');
    $this->assertRaw(String::format('The %label page variant has been added.', array('%label' => 'First')));
  }

  /**
   * Tests adding a block to a variant.
   */
  protected function doTestAddBlock() {
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

  /**
   * Tests editing a page variant.
   */
  protected function doTestEditPageVariant() {
    if (!$block = $this->findBlockByLabel('foo', 'First', 'User account menu')) {
      $this->fail('Block not found');
      return;
    }

    $block_config = $block->getConfiguration();
    $this->drupalGet('admin/structure/block_page/manage/foo');
    $this->clickLink('Edit');
    $this->assertTitle('Edit First page variant | Drupal');
    $this->assertOptionSelected('edit-blocks-' . $block_config['uuid'] . '-region', 'top');
    $this->assertOptionSelected('edit-blocks-' . $block_config['uuid'] . '-weight', 0);

    $form_name = 'blocks[' . $block_config['uuid'] . ']';
    $edit = array(
      $form_name . '[region]' => 'bottom',
      $form_name . '[weight]' => -10,
    );
    $this->drupalPostForm(NULL, $edit, 'Update page variant');
    $this->assertRaw(String::format('The %label page variant has been updated.', array('%label' => 'First')));
    $this->clickLink('Edit');
    $this->assertOptionSelected('edit-blocks-' . $block_config['uuid'] . '-region', 'bottom');
    $this->assertOptionSelected('edit-blocks-' . $block_config['uuid'] . '-weight', -10);
  }

  /**
   * Finds a block based on its block page, variant, and block label.
   *
   * @param string $block_page_id
   *   The ID of the block page.
   * @param string $page_variant_label
   *   The label of the page variant.
   * @param string $block_label
   *   The label of the block.
   *
   * @return \Drupal\block\BlockPluginInterface|null
   */
  protected function findBlockByLabel($block_page_id, $page_variant_label, $block_label) {
    $block_page = \Drupal::entityManager()->getStorage('block_page')->load($block_page_id);
    foreach ($block_page->getPluginBag() as $page_variant) {
      if ($page_variant->label() != $page_variant_label) {
        continue;
      }
      foreach ($page_variant->getRegionAssignments() as $blocks) {
        foreach ($blocks as $block) {
          if ($block->label() == $block_label) {
            return $block;
          }
        }
      }
    }
  }

}
