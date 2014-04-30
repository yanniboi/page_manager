<?php

/**
 * @file
 * Contains \Drupal\block_group\Entity\BlockGroupViewBuilder.
 */

namespace Drupal\block_group\Entity;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Provides a view builder for block groups.
 */
class BlockGroupViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = array(), $view_mode = 'full', $langcode = NULL) {
    $build = array();
    $account = \Drupal::currentUser();
    /** @var $entities \Drupal\block_group\BlockGroupInterface[] */
    foreach ($entities as $key => $entity) {
      foreach ($entity->getRegionAssignments() as $region => $blocks) {
        $region_name = drupal_html_class("block-region-$region");
        $build[$key][$region]['#prefix'] = '<div class="' . $region_name . '">';
        $build[$key][$region]['#suffix'] = '</div>';

        /** @var $blocks \Drupal\block\BlockPluginInterface[] */
        foreach ($blocks as $block_id => $block) {
          if ($block->access($account)) {
            $row = $block->build();
            $block_name = drupal_html_class("block-$block_id");
            $row['#prefix'] = '<div class="' . $block_name . '">';
            $row['#suffix'] = '</div>';

            $build[$key][$region][$block_id] = $row;
          }
        }
      }
    }
    return $build;
  }

}
