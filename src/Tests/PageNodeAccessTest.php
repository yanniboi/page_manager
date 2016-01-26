<?php

/**
 * @file
 * Contains \Drupal\page_manager\Tests\PageNodeAccessTest.
 */

namespace Drupal\page_manager\Tests;

use Drupal\page_manager\Entity\Page;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Tests the access for an overridden route, specifically /node/{node}.
 *
 * @group page_manager
 */
class PageNodeAccessTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['page_manager', 'node', 'user'];

  /**
   * @var \Drupal\page_manager\PageInterface
   */
  protected $page;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Remove the 'access content' permission from anonymous and auth users.
    Role::load(RoleInterface::ANONYMOUS_ID)->revokePermission('access content')->save();
    Role::load(RoleInterface::AUTHENTICATED_ID)->revokePermission('access content')->save();

    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Page']);
    $this->drupalPlaceBlock('page_title_block');
    $this->page = Page::load('node_view');
  }

  /**
   * Tests that a user role condition controls the node view page.
   */
  public function testUserRoleAccessCondition() {
    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $node = $this->drupalCreateNode(['type' => 'page']);
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(200);
    $this->assertText($node->label());
    $this->assertTitle($node->label() . ' | Drupal');

    // Add a variant and an access condition.
    $page_variant = PageVariant::create([
      'variant' => 'block_display',
      'id' => 'block_page',
      'label' => 'Block page',
      'page' => $this->page->id(),
    ]);
    $page_variant->getVariantPlugin()->setConfiguration(['page_title' => 'The overridden page']);
    $page_variant->save();

    $this->page->addAccessCondition([
      'id' => 'user_role',
      'roles' => [
        RoleInterface::AUTHENTICATED_ID => RoleInterface::AUTHENTICATED_ID,
      ],
      'context_mapping' => [
        'user' => 'current_user',
      ],
    ]);
    $this->page->save();

    $this->drupalLogout();
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(403);
    $this->assertNoText($node->label());
    $this->assertTitle('Access denied | Drupal');

    $this->drupalLogin($this->drupalCreateUser());
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(403);
    $this->assertNoText($node->label());
    $this->assertTitle('Access denied | Drupal');

    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(200);
    $this->assertNoText($node->label());
    $this->assertTitle('The overridden page | Drupal');
  }

}
