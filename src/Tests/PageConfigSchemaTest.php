<?php

/**
 * @file
 * Contains \Drupal\page_manager\Tests\PageConfigSchemaTest.
 */

namespace Drupal\page_manager\Tests;

use Drupal\config\Tests\SchemaCheckTestTrait;
use Drupal\KernelTests\KernelTestBase;
use Drupal\page_manager\Entity\Page;
use Drupal\ctools\Entity\DisplayVariant;

/**
 * Ensures that page entities have valid config schema.
 *
 * @group page_manager
 */
class PageConfigSchemaTest extends KernelTestBase {

  use SchemaCheckTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['page_manager', 'panels', 'block', 'node', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['page_manager']);
    $this->installConfig(['panels']);
  }

  /**
   * Tests whether the page entity config schema is valid.
   */
  public function testValidPageConfigSchema() {
    $id = 'node_view';
    /** @var \Drupal\ctools\Entity\DisplayInterface $page */
    $page = Page::load($id);

    // Add an access condition.
    $page->addAccessCondition([
      'id' => 'node_type',
      'bundles' => [
        'article' => 'article',
      ],
      'negate' => TRUE,
      'context_mapping' => [
        'node' => 'node',
      ],
    ]);
    $page->save();

    $display_variant_id = 'block_page';
    // Add a block variant.
    $display_variant = DisplayVariant::create([
      'variant' => 'block_display',
      'id' => $display_variant_id,
      'label' => 'Block page',
      'display_entity_id' => $page->id(),
      'display_entity_type' => 'page',
    ]);
    $display_variant->save();
    $page->addVariant($display_variant);
    /** @var \Drupal\page_manager\Plugin\DisplayVariant\PageBlockDisplayVariant $variant_plugin */
    $variant_plugin = $display_variant->getVariantPlugin();

    // Add a selection condition.
    $display_variant->addSelectionCondition([
      'id' => 'node_type',
      'bundles' => [
        'page' => 'page',
      ],
      'context_mapping' => [
        'node' => 'node',
      ],
    ]);

    // Add a block.
    $variant_plugin->addBlock([
      'id' => 'entity_view:node',
      'label' => 'View the node',
      'provider' => 'page_manager',
      'label_display' => 'visible',
      'view_mode' => 'default',
    ]);
    $display_variant->save();

    $page_config = \Drupal::config("page_manager.page.$id");
    $this->assertSame($page_config->get('id'), $id);
    $variant_config = \Drupal::config("ctools.display_variant.$display_variant_id");
    $this->assertSame($variant_config->get('id'), $display_variant_id);

    $this->assertConfigSchema(\Drupal::service('config.typed'), $page_config->getName(), $page_config->get());
    $this->assertConfigSchema(\Drupal::service('config.typed'), $variant_config->getName(), $variant_config->get());
  }

}
