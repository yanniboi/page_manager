<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\PageVariantInterface.
 */

namespace Drupal\block_page\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * @todo.
 */
interface PageVariantInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the user-facing page variant label.
   *
   * @return string
   *   The page variant label.
   */
  public function label();

  /**
   * @todo.
   */
  public function id();

  /**
   * @todo.
   */
  public function getWeight();

  /**
   * @todo.
   *
   * @param int $weight
   */
  public function setWeight($weight);

  /**
   * @todo.
   */
  public function getBlock($block_id);

  /**
   * Adds a block to this page variant.
   *
   * @param array $configuration
   *   An array of block configuration.
   *
   * @return string
   *   The block ID.
   */
  public function addBlock(array $configuration);

  /**
   * Updates the configuration of a specific block plugin.
   *
   * @param string $block_id
   *   The block ID.
   * @param array $configuration
   *   The array of configuration to set.
   *
   * @return $this
   */
  public function updateBlock($block_id, array $configuration);

  /**
   * Returns the region a specific block is assigned to.
   *
   * @param string $block_id
   *   The block ID.
   *
   * @return string
   *   The machine name of the region this block is assigned to.
   */
  public function getRegionAssignment($block_id);

  /**
   * Returns an array of regions and their block plugins.
   *
   * @return array
   *   The array is first keyed by region machine name, with the values
   *   containing an array keyed by block ID, with block plugin instances as the
   *   values.
   */
  public function getRegionAssignments();

  /**
   * Returns the human-readable list of regions keyed by machine name.
   *
   * @return array
   */
  public function getRegionNames();

  /**
   * Returns the human-readable name of a specific region.
   *
   * @param string $region
   *   The machine name of a region.
   *
   * @return string
   *   The human-readable name of a region.
   */
  public function getRegionName($region);

  /**
   * Returns the number of blocks contained in this group.
   *
   * @return int
   */
  public function getBlockCount();

  /**
   * @todo.
   *
   * @return bool
   */
  public function access();

}
