<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Tests\PageManagerAdminTest.
 */

namespace Drupal\page_manager_ui\Tests;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;
use Drupal\page_manager\Entity\Page;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the admin UI for page entities.
 *
 * @group page_manager_ui
 */
class PageManagerVaruantAdminTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['block', 'page_manager_ui', 'page_manager_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalPlaceBlock('system_branding_block');
    $this->drupalPlaceBlock('page_title_block');

    \Drupal::service('theme_handler')->install(['bartik', 'classy']);
    $this->config('system.theme')->set('admin', 'classy')->save();

    $this->drupalLogin($this->drupalCreateUser([
      'administer pages',
      'access administration pages',
      'view the administration theme'
    ]));

    // Remove the default node_view page to start with a clean UI.
    Page::load('node_view')->delete();
  }

  /**
   * Tests the Page Manager admin UI.
   */
  public function testAdmin() {
    $this->doTestAddVariant();
    $this->doTestAbandonVariant();
  }
}