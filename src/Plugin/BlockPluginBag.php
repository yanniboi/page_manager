<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\BlockPluginBag.
 */

namespace Drupal\block_page\Plugin;

use Drupal\block\BlockPluginInterface;
use Drupal\Core\Plugin\DefaultPluginBag;

/**
 * Provides a collection of block plugins.
 */
class BlockPluginBag extends DefaultPluginBag {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\block\BlockPluginInterface
   */
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

  public function getBlockRegion($block_id) {
    $configuration = $this->get($block_id)->getConfiguration();
    return isset($configuration['region']) ? $configuration['region'] : NULL;
  }
  public function setBlockRegion($block_id, $region) {
    $configuration = $this->get($block_id)->getConfiguration();
    $configuration['region'] = $region;
    $this->setInstanceConfiguration($block_id, $configuration);
    return $this;
  }

  public function getAllByRegion() {
    $region_assignments = array();
    /** @var $block \Drupal\block\BlockPluginInterface */
    foreach ($this as $block_id => $block) {
      $region = $this->getBlockRegion($block_id);
      $region_assignments[$region][$block_id] = $block;
    }
    foreach ($region_assignments as $region => $region_assignment) {
      uasort($region_assignment, function (BlockPluginInterface $a, BlockPluginInterface $b) {
        $a_config = $a->getConfiguration();
        $a_weight = isset($a_config['weight']) ? $a_config['weight'] : 0;
        $b_config = $b->getConfiguration();
        $b_weight = isset($b_config['weight']) ? $b_config['weight'] : 0;
        if ($a_weight == $b_weight) {
          return strcmp($a->label(), $b->label());
        }
        return $a_weight > $b_weight ? 1 : -1;
      });
      $region_assignments[$region] = $region_assignment;
    }
    return $region_assignments;

  }

}
