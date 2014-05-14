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
   * Adds a new page variant to the block page.
   *
   * @param array $configuration
   *
   * @return string
   *   The page variant ID.
   */
  public function addPageVariant(array $configuration);

  /**
   * Retrieves a specific page variant.
   *
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface
   *   The page variant object.
   */
  public function getPageVariant($page_variant_id);

  /**
   * Removes a specific page variant.
   *
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return $this
   */
  public function removePageVariant($page_variant_id);

  /**
   * Returns the page variants available for the block page.
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface[]
   *   An array of the page variants.
   */
  public function getPageVariants();

  /**
   * Selects the page variant to use for this block page.
   *
   * This loops through the available page variants and checks each for access,
   * returning the first one that is accessible.
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface|null
   *   Either the first accessible page variant, or NULL if none are accessible.
   */
  public function selectPageVariant();

}
