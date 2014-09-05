<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\PageTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the Page entity.
 *
 * @coversDefaultClass \Drupal\page_manager\Entity\Page
 *
 * @group Drupal
 * @group PageManager
 */
class PageTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Tests the Page entity',
      'description' => '',
      'group' => 'Page Manager',
    );
  }

  /**
   * @covers ::isFallbackPage
   *
   * @dataProvider providerTestIsFallbackPage
   */
  public function testIsFallbackPage($id, $expected) {
    $page = $this->getMockBuilder('Drupal\page_manager\Entity\Page')
      ->setConstructorArgs(array(array('id' => $id), 'page'))
      ->setMethods(array('configFactory'))
      ->getMock();

    $config_factory = $this->getConfigFactoryStub(array(
      'page_manager.settings' => array(
        'fallback_page' => 'fallback',
      )));
    $page->expects($this->once())
      ->method('configFactory')
      ->will($this->returnValue($config_factory));

    $this->assertSame($expected, $page->isFallbackPage());
  }

  /**
   * Provides test data for testIsFallbackPage().
   */
  public function providerTestIsFallbackPage() {
    $data = array();
    $data[] = array('foo', FALSE);
    $data[] = array('fallback', TRUE);
    return $data;
  }

}
