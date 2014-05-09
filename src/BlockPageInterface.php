<?php

/**
 * @file
 * Contains \Drupal\block_page\BlockPageInterface.
 */

namespace Drupal\block_page;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Config\Entity\EntityWithPluginBagInterface;

/**
 * Provides an interface for block page objects.
 */
interface BlockPageInterface extends ConfigEntityInterface, EntityWithPluginBagInterface {

  /**
   * Returns the path for this block page.
   *
   * @return string
   */
  public function getPath();

  /**
   * @todo.
   *
   * @param array $configuration
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface
   */
  public function addPageVariant(array $configuration);

  /**
   * @todo.
   *
   * @param string $page_variant_id
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface
   */
  public function getPageVariant($page_variant_id);

  /**
   * @todo.
   *
   * @param string $page_variant_id
   *
   * @return $this
   */
  public function removePageVariant($page_variant_id);

  /**
   * @todo.
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface|null
   */
  public function selectPageVariant();

}
