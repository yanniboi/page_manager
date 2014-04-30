<?php

/**
 * @file
 * Contains \Drupal\block_group\BlockGroupInterface.
 */

namespace Drupal\block_group;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Config\Entity\EntityWithPluginBagInterface;

/**
 * Provides an interface for block group objects.
 */
interface BlockGroupInterface extends ConfigEntityInterface, EntityWithPluginBagInterface {

  /**
   * Adds a block to this block group.
   *
   * @param array $configuration
   *   An array of block configuration.
   *
   * @return string
   *   The block ID.
   */
  public function addBlockToGroup(array $configuration);

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
   * Sets the region for a specific block plugin.
   *
   * @param string $block_id
   *   The block ID.
   * @param string $region
   *   The machine name of the region.
   *
   * @return $this
   */
  public function setRegionAssignment($block_id, $region);

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

}
