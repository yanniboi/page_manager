<?php

/**
 * @file
 * Contains \Drupal\block_page\Tests\ContextHandlerTest.
 */

namespace Drupal\block_page\Tests;

use Drupal\block_page\ContextHandler;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the ContextHandler class.
 *
 * @coversDefaultClass \Drupal\block_page\ContextHandler
 *
 * @group Drupal
 * @group BlockPage
 */
class ContextHandlerTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Tests the ContextHandler',
      'description' => '',
      'group' => 'Block Page',
    );
  }

  /**
   * @covers ::checkRequirements
   * @covers ::getValidContexts
   *
   * @dataProvider providerTestCheckRequirements
   */
  public function testCheckRequirements($contexts, $requirements, $expected) {
    $typed_data_manager = $this->getMockBuilder('Drupal\Core\TypedData\TypedDataManager')
      ->disableOriginalConstructor()
      ->getMock();
    $context_handler = new ContextHandler($typed_data_manager);
    $this->assertSame($expected, $context_handler->checkRequirements($contexts, $requirements));
  }

  public function providerTestCheckRequirements() {
    $data = array();

    $requirement_any = $this->getMock('Drupal\Core\TypedData\DataDefinitionInterface');
    $requirement_any->expects($this->atLeastOnce())
      ->method('isRequired')
      ->will($this->returnValue(TRUE));

    $requirement_optional = $this->getMock('Drupal\Core\TypedData\DataDefinitionInterface');
    $requirement_optional->expects($this->atLeastOnce())
      ->method('isRequired')
      ->will($this->returnValue(FALSE));

    $requirement_any = $this->getMock('Drupal\Core\TypedData\DataDefinitionInterface');
    $requirement_any->expects($this->atLeastOnce())
      ->method('isRequired')
      ->will($this->returnValue(TRUE));
    $requirement_any->expects($this->atLeastOnce())
      ->method('getDataType')
      ->will($this->returnValue('any'));
    $requirement_any->expects($this->atLeastOnce())
      ->method('getConstraints')
      ->will($this->returnValue(array()));

    $context_any = $this->getMock('Drupal\Component\Plugin\Context\ContextInterface');
    $context_any->expects($this->atLeastOnce())
      ->method('getContextDefinition')
      ->will($this->returnValue(array()));

    $requirement_specific = $this->getMock('Drupal\Core\TypedData\DataDefinitionInterface');
    $requirement_specific->expects($this->atLeastOnce())
      ->method('isRequired')
      ->will($this->returnValue(TRUE));
    $requirement_specific->expects($this->atLeastOnce())
      ->method('getDataType')
      ->will($this->returnValue('foo'));
    $requirement_specific->expects($this->atLeastOnce())
      ->method('getConstraints')
      ->will($this->returnValue(array('bar' => 'baz')));

    $context_constraint_mismatch = $this->getMock('Drupal\Component\Plugin\Context\ContextInterface');
    $context_constraint_mismatch->expects($this->atLeastOnce())
      ->method('getContextDefinition')
      ->will($this->returnValue(array('type' => 'foo')));
    $context_datatype_mismatch = $this->getMock('Drupal\Component\Plugin\Context\ContextInterface');
    $context_datatype_mismatch->expects($this->atLeastOnce())
      ->method('getContextDefinition')
      ->will($this->returnValue(array('type' => 'fuzzy')));

    $context_specific = $this->getMock('Drupal\Component\Plugin\Context\ContextInterface');
    $context_specific->expects($this->atLeastOnce())
      ->method('getContextDefinition')
      ->will($this->returnValue(array('type' => 'foo', 'constraints' => array('bar' => 'baz'))));

    $data[] = array(array(), array(), TRUE);
    $data[] = array(array(), array($requirement_any), FALSE);
    $data[] = array(array(), array($requirement_optional), TRUE);
    $data[] = array(array(), array($requirement_any, $requirement_optional), FALSE);
    $data[] = array(array($context_any), array($requirement_any), TRUE);
    $data[] = array(array($context_constraint_mismatch), array($requirement_specific), FALSE);
    $data[] = array(array($context_datatype_mismatch), array($requirement_specific), TRUE);
    $data[] = array(array($context_specific), array($requirement_specific), TRUE);

    return $data;
  }

}
