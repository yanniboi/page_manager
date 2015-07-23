<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\PageAccessTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\page_manager\Entity\PageAccess;
use Drupal\page_manager\PageExecutableInterface;
use Drupal\page_manager\PageInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests access for Page entities.
 *
 * @coversDefaultClass \Drupal\page_manager\Entity\PageAccess
 *
 * @group PageManager
 */
class PageAccessTest extends UnitTestCase {

  /**
   * The context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $contextHandler;

  /**
   * @var \Drupal\Core\Entity\EntityTypeInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityType;

  /**
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $pageAccess;

  /**
   * @covers ::__construct
   */
  public function setUp() {
    parent::setUp();
    $this->contextHandler = $this->getMock(ContextHandlerInterface::class);
    $this->entityType = $this->getMock(EntityTypeInterface::class);

    $module_handler = $this->getMock(ModuleHandlerInterface::class);
    $module_handler->expects($this->any())
      ->method('invokeAll')
      ->will($this->returnValue([]));

    $this->pageAccess = new PageAccess($this->entityType, $this->contextHandler);
    $this->pageAccess->setModuleHandler($module_handler);
  }

  /**
   * @covers ::checkAccess
   */
  public function testAccessView() {
    $executable = $this->getMock(PageExecutableInterface::class);
    $executable->expects($this->once())
      ->method('getContexts')
      ->will($this->returnValue([]));

    $page = $this->getMock(PageInterface::class);
    $page->expects($this->once())
      ->method('getExecutable')
      ->will($this->returnValue($executable));
    $page->expects($this->once())
      ->method('getAccessConditions')
      ->will($this->returnValue([]));
    $page->expects($this->once())
      ->method('getAccessLogic')
      ->will($this->returnValue('and'));
    $page->expects($this->once())
      ->method('status')
      ->will($this->returnValue(TRUE));

    $account = $this->getMock(AccountInterface::class);

    $this->assertTrue($this->pageAccess->access($page, 'view', NULL, $account));
  }

  /**
   * @covers ::checkAccess
   */
  public function testAccessViewDisabled() {
    $page = $this->getMock(PageInterface::class);
    $page->expects($this->once())
      ->method('status')
      ->will($this->returnValue(FALSE));
    $account = $this->getMock(AccountInterface::class);

    $page->expects($this->once())
      ->method('getCacheTags')
      ->willReturn(['page:1']);

    $this->assertFalse($this->pageAccess->access($page, 'view', NULL, $account));
  }

  /**
   * @covers ::checkAccess
   *
   * @dataProvider providerTestAccessDelete
   */
  public function testAccessDelete($is_new, $is_fallback, $expected) {
    $this->entityType->expects($this->any())
      ->method('getAdminPermission')
      ->will($this->returnValue('test permission'));

    $page = $this->getMock(PageInterface::class);
    $page->expects($this->any())
      ->method('isNew')
      ->will($this->returnValue($is_new));
    $page->expects($this->any())
      ->method('isFallbackPage')
      ->will($this->returnValue($is_fallback));

    // Ensure that the cache tag is added for the temporary conditions.
    if ($is_new || $is_fallback) {
      $page->expects($this->once())
        ->method('getCacheTags')
        ->willReturn(['page:1']);
    }

    $account = $this->getMock(AccountInterface::class);
    $account->expects($this->any())
      ->method('hasPermission')
      ->with('test permission')
      ->will($this->returnValue(TRUE));

    $this->assertSame($expected, $this->pageAccess->access($page, 'delete', NULL, $account));
  }

  /**
   * Provides data for testAccessDelete().
   */
  public function providerTestAccessDelete() {
    $data = [];
    $data[] = [TRUE, FALSE, FALSE];
    $data[] = [FALSE, TRUE, FALSE];
    $data[] = [TRUE, TRUE, FALSE];
    $data[] = [FALSE, FALSE, TRUE];
    return $data;
  }

}
