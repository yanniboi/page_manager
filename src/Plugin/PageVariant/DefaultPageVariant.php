<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\PageVariant\DefaultPageVariant.
 */

namespace Drupal\page_manager\Plugin\PageVariant;

use Drupal\page_manager\Plugin\PageVariantBase;

/**
 * Provides a default page variant.
 */
class DefaultPageVariant extends PageVariantBase {

  /**
   * {@inheritdoc}
   */
  public function getRegionNames() {
    // @todo Reference an external object of some kind, like a Layout.
    return array(
      'top' => 'Top',
      'bottom' => 'Bottom',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = array();
    $account = \Drupal::currentUser();
    foreach ($this->getRegionAssignments() as $region => $blocks) {
      if (!$blocks) {
        continue;
      }

      $region_name = drupal_html_class("block-region-$region");
      $build[$region]['#prefix'] = '<div class="' . $region_name . '">';
      $build[$region]['#suffix'] = '</div>';

      /** @var $blocks \Drupal\block\BlockPluginInterface[] */
      foreach ($blocks as $block_id => $block) {
        if ($block->access($account)) {
          $row = $block->build();
          $block_name = drupal_html_class("block-$block_id");
          $row['#prefix'] = '<div class="' . $block_name . '">';
          $row['#suffix'] = '</div>';

          $build[$region][$block_id] = $row;
        }
      }
    }
    return $build;
  }

}
