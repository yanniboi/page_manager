<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\CurrentUserContextTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\page_manager\EventSubscriber\CurrentUserContext;
use Drupal\user\UserInterface;

/**
 * Tests the current user context.
 *
 * @coversDefaultClass \Drupal\page_manager\EventSubscriber\CurrentUserContext
 *
 * @group PageManager
 */
class CurrentUserContextTest extends PageContextTestBase {

  public function testOnPageContext() {
    $account = $this->getMock(AccountInterface::class);
    $account->expects($this->once())
      ->method('id')
      ->will($this->returnValue(1));
    $user = $this->getMock(TestUserInterface::class);

    $this->typedDataManager->expects($this->any())
      ->method('create')
      ->willReturn(EntityAdapter::createFromEntity($user));
    $this->typedDataManager->expects($this->any())
      ->method('getDefaultConstraints')
      ->will($this->returnValue([]));
    $this->typedDataManager->expects($this->any())
      ->method('createDataDefinition')
      ->will($this->returnCallback(function ($type) {
        return new DataDefinition(['type' => $type]);
      }));

    $this->executable->expects($this->once())
      ->method('addContext')
      ->with('current_user', $this->isInstanceOf(Context::class));

    $entity_manager = $this->getMock(EntityManagerInterface::class);
    $user_storage = $this->getMock(EntityStorageInterface::class);
    $user_storage->expects($this->once())
      ->method('load')
      ->with(1)
      ->will($this->returnValue($user));
    $entity_manager->expects($this->any())
      ->method('getStorage')
      ->with('user')
      ->will($this->returnValue($user_storage));

    $route_param_context = new CurrentUserContext($account, $entity_manager);
    $route_param_context->onPageContext($this->event);
  }

}

/**
 * Provides a testable version of UserInterface.
 *
 * @see https://github.com/sebastianbergmann/phpunit-mock-objects/commit/96a6794
 */
interface TestUserInterface extends \Iterator, UserInterface {
}
