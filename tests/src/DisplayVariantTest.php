<?php

/**
 * @file
 * Contains \Drupal\page_manager\Tests\DisplayVariantTest.
 */

namespace Drupal\page_manager\Tests;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the display variant plugin.
 *
 * @coversDefaultClass \Drupal\page_manager\Plugin\VariantBase
 *
 * @group Drupal
 * @group PageManager
 */
class DisplayVariantTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Page Manager display variant',
      'description' => '',
      'group' => 'Page Manager',
    );
  }

  /**
   * Sets up a display variant plugin for testing.
   *
   * @param array $configuration
   *   An array of plugin configuration.
   * @param array $definition
   *   The plugin definition array.
   *
   * @return \Drupal\page_manager\Plugin\VariantBase|\PHPUnit_Framework_MockObject_MockObject
   *   A mocked display variant plugin.
   */
  public function setUpDisplayVariant($configuration = array(), $definition = array()) {
    return $this->getMockBuilder('Drupal\page_manager\Plugin\VariantBase')
      ->setConstructorArgs(array($configuration, 'test', $definition))
      ->setMethods(array('getRegionNames', 'access', 'render', 'getBlockBag', 'getSelectionConditions'))
      ->getMock();
  }

  /**
   * Tests the label() method.
   *
   * @covers ::label
   */
  public function testLabel() {
    $display_variant = $this->setUpDisplayVariant(array('label' => 'foo'));
    $this->assertSame('foo', $display_variant->label());
  }

  /**
   * Tests the label() method using a default value.
   *
   * @covers ::label
   */
  public function testLabelDefault() {
    $display_variant = $this->setUpDisplayVariant();
    $this->assertSame('', $display_variant->label());
  }

  /**
   * Tests the getWeight() method.
   *
   * @covers ::getWeight
   */
  public function testGetWeight() {
    $display_variant = $this->setUpDisplayVariant(array('weight' => 5));
    $this->assertSame(5, $display_variant->getWeight());
  }

  /**
   * Tests the getWeight() method using a default value.
   *
   * @covers ::getWeight
   */
  public function testGetWeightDefault() {
    $display_variant = $this->setUpDisplayVariant();
    $this->assertSame(0, $display_variant->getWeight());
  }

  /**
   * Tests the getRegionName() method.
   *
   * @covers ::getRegionName
   *
   * @dataProvider providerTestGetRegionName
   */
  public function testGetRegionName($region_name, $expected) {
    $display_variant = $this->setUpDisplayVariant();
    $display_variant->expects($this->once())
      ->method('getRegionNames')
      ->will($this->returnValue(array(
        'test1' => 'Test 1',
        'test2' => 'Test 2',
      )));
    $this->assertSame($expected, $display_variant->getRegionName($region_name));
  }

  public function providerTestGetRegionName() {
    return array(
      array('test1', 'Test 1'),
      array('test2', 'Test 2'),
      array('test3', ''),
    );
  }

  /**
   * Tests the getRegionAssignments() method.
   *
   * @covers ::getRegionAssignments
   *
   * @dataProvider providerTestGetRegionAssignments
   */
  public function testGetRegionAssignments($expected, $blocks = array()) {
    $block_bag = $this->getMockBuilder('Drupal\page_manager\Plugin\BlockPluginBag')
      ->disableOriginalConstructor()
      ->getMock();
    $block_bag->expects($this->once())
      ->method('getAllByRegion')
      ->will($this->returnValue($blocks));

    $display_variant = $this->setUpDisplayVariant();
    $display_variant->expects($this->once())
      ->method('getBlockBag')
      ->will($this->returnValue($block_bag));
    $display_variant->expects($this->once())
      ->method('getRegionNames')
      ->will($this->returnValue(array(
        'test1' => 'Test 1',
        'test2' => 'Test 2',
      )));

    $this->assertSame($expected, $display_variant->getRegionAssignments());
  }

  public function providerTestGetRegionAssignments() {
    return array(
      array(
        array(
          'test1' => array(),
          'test2' => array(),
        ),
      ),
      array(
        array(
          'test1' => array('foo'),
          'test2' => array(),
        ),
        array(
          'test1' => array('foo'),
        ),
      ),
      array(
        array(
          'test1' => array(),
          'test2' => array(),
        ),
        array(
          'test3' => array('foo'),
        ),
      ),
      array(
        array(
          'test1' => array(),
          'test2' => array('foo'),
        ),
        array(
          'test2' => array('foo'),
          'test3' => array('bar'),
        ),
      ),
    );
  }

  /**
   * Tests the getConfiguration() method.
   *
   * @covers ::getConfiguration
   */
  public function testGetConfiguration() {
    $block_bag = $this->getMockBuilder('Drupal\page_manager\Plugin\BlockPluginBag')
      ->disableOriginalConstructor()
      ->getMock();
    $block_bag->expects($this->once())
      ->method('getConfiguration')
      ->will($this->returnValue(array()));
    $condition_bag = $this->getMockBuilder('Drupal\Core\Condition\ConditionPluginBag')
      ->disableOriginalConstructor()
      ->getMock();
    $condition_bag->expects($this->once())
      ->method('getConfiguration')
      ->will($this->returnValue(array()));
    $display_variant = $this->setUpDisplayVariant();
    $display_variant->expects($this->once())
      ->method('getBlockBag')
      ->will($this->returnValue($block_bag));
    $display_variant->expects($this->once())
      ->method('getSelectionConditions')
      ->will($this->returnValue($condition_bag));

    $expected = array(
      'id' => 'test',
      'blocks' => array(),
      'selection_conditions' => array(),
      'label' => '',
      'uuid' => '',
      'weight' => 0,
      'selection_logic' => 'and',
    );
    $this->assertSame($expected, $display_variant->getConfiguration());
  }

}
