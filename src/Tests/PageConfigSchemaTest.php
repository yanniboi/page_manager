<?php

/**
 * @file
 * Contains \Drupal\page_manager\Tests\PageConfigSchemaTest.
 */

namespace Drupal\page_manager\Tests;

use Drupal\config\Tests\SchemaCheckTestTrait;
use Drupal\page_manager\Entity\Page;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\simpletest\KernelTestBase;

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
  public static $modules = ['page_manager', 'block', 'node', 'user'];

  /**
   * Tests whether the page entity config schema is valid.
   */
  public function testValidPageConfigSchema() {
    $id = strtolower($this->randomMachineName());
    /** @var \Drupal\page_manager\PageInterface $page */
    $page = Page::create([
      'id' => $id,
      'label' => $this->randomMachineName(),
      'path' => '/node/{node}',
    ]);

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

    $page_variant_id = 'block_page';
    // Add a block variant.
    $page_variant = PageVariant::create([
      'variant' => 'block_display',
      'id' => $page_variant_id,
      'label' => 'Block page',
      'page' => $page->id(),
    ]);
    $page_variant->save();
    $page->addVariant($page_variant);
    /** @var \Drupal\page_manager\Plugin\DisplayVariant\PageBlockDisplayVariant $variant_plugin */
    $variant_plugin = $page_variant->getVariantPlugin();

    // Add a selection condition.
    $page_variant->addSelectionCondition([
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
    $page_variant->save();

    $page_config = \Drupal::config("page_manager.page.$id");
    $this->assertEqual($page_config->get('id'), $id);
    $variant_config = \Drupal::config("page_manager.page_variant.$page_variant_id");
    $this->assertEqual($variant_config->get('id'), $page_variant_id);

    $this->assertConfigSchema(\Drupal::service('config.typed'), $page_config->getName(), $page_config->get());
    $this->assertConfigSchema(\Drupal::service('config.typed'), $variant_config->getName(), $variant_config->get());
  }

}
