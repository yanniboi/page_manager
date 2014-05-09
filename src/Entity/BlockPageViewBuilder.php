<?php

/**
 * @file
 * Contains \Drupal\block_page\Entity\BlockPageViewBuilder.
 */

namespace Drupal\block_page\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Provides a view builder for block pages.
 */
class BlockPageViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $account = \Drupal::currentUser();
    $build = array();
    /** @var $entity \Drupal\block_page\BlockPageInterface */
    if ($page_variant = $entity->selectPageVariant()) {
      foreach ($page_variant->getRegionAssignments() as $region => $blocks) {
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
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = array(), $view_mode = 'full', $langcode = NULL) {
    $build = array();
    foreach ($entities as $key => $entity) {
      $build[$key] = $this->view($entity, $view_mode, $langcode);
    }
    return $build;
  }

}
