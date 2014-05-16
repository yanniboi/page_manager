<?php

/**
 * @file
 * Contains \Drupal\block_page\BlockPageInterface.
 */

namespace Drupal\block_page;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginBagsInterface;

/**
 * Provides an interface for block page objects.
 */
interface BlockPageInterface extends ConfigEntityInterface, EntityWithPluginBagsInterface {

  /**
   * Returns the path for the block page.
   *
   * @return string
   *   The path for the block page.
   */
  public function getPath();

  /**
   * Adds a new page variant to the block page.
   *
   * @param array $configuration
   *   An array of configuration for the new page variant.
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
   * Selects the page variant to use for the block page.
   *
   * This loops through the available page variants and checks each for access,
   * returning the first one that is accessible.
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface|null
   *   Either the first accessible page variant, or NULL if none are accessible.
   */
  public function selectPageVariant();

  /**
   * Returns the conditions used for determining access for this block page.
   *
   * @return \Drupal\Core\Condition\ConditionInterface[]|\Drupal\block_page\Plugin\ConditionPluginBag
   *   An array of configured condition plugins.
   */
  public function getAccessConditions();

  /**
   * Adds a new access condition to the block page.
   *
   * @param array $configuration
   *   An array of configuration for the new access condition.
   *
   * @return string
   *   The access condition ID.
   */
  public function addAccessCondition(array $configuration);

  /**
   * Retrieves a specific access condition.
   *
   * @param string $page_variant_id
   *   The access condition ID.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The access condition object.
   */
  public function getAccessCondition($page_variant_id);

  /**
   * Removes a specific access condition.
   *
   * @param string $page_variant_id
   *   The access condition ID.
   *
   * @return $this
   */
  public function removeAccessCondition($page_variant_id);

}
