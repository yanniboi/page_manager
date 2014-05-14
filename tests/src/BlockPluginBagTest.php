<?php

/**
 * @file
 * Contains \Drupal\block_page\Tests\BlockPluginBagTest.
 */

namespace Drupal\block_page\Tests;

use Drupal\block_page\Plugin\BlockPluginBag;
use Drupal\Tests\UnitTestCase;

/**
 * @todo.
 *
 * @coversDefaultClass \Drupal\block_page\Plugin\BlockPluginBag
 *
 * @group Drupal
 * @group BlockPage
 */
class BlockPluginBagTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Block page bag',
      'description' => '',
      'group' => 'Block Group',
    );
  }

  /**
   * @covers ::getAllByRegion
   */
  public function testGetAllByRegion() {
    $blocks = array(
      'foo' => array(
        'id' => 'foo',
        'label' => 'Foo',
        'plugin' => 'system_powered_by_block',
        'region' => 'bottom',
      ),
      'bar' => array(
        'id' => 'bar',
        'label' => 'Bar',
        'plugin' => 'system_powered_by_block',
        'region' => 'top',
      ),
    );
    $plugins = array();
    $plugin_map = array();
    foreach ($blocks as $block_id => $block) {
      $plugin = $this->getMock('Drupal\block\BlockPluginInterface');
      $plugin->expects($this->any())
        ->method('label')
        ->will($this->returnValue($block['label']));
      $plugin->expects($this->any())
        ->method('getConfiguration')
        ->will($this->returnValue($block));
      $plugins[$block_id] = $plugin;
      $plugin_map[] = array($block_id, $block, $plugin);
    }
    $block_manager = $this->getMock('Drupal\block\BlockManagerInterface');
    $block_manager->expects($this->exactly(2))
      ->method('createInstance')
      ->will($this->returnValueMap($plugin_map));

    $block_plugin_bag = new BlockPluginBag($block_manager, $blocks);
    $expected = array(
      'bottom' => array('foo' => $plugins['foo']),
      'top' => array('bar' => $plugins['bar']),
    );
    $this->assertSame($expected, $block_plugin_bag->getAllByRegion());
  }

}
