<?php

/**
 * @file
 * Contains \Drupal\block_page\Controller\BlockPageController.
 */

namespace Drupal\block_page\Controller;

use Drupal\block_page\BlockPageInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route controllers for block page.
 */
class BlockPageController extends ControllerBase {

  /**
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   *
   * @return string
   *   The title for the block page edit form.
   */
  public function editBlockPageTitle(BlockPageInterface $block_page) {
    return $this->t('Edit %label block page', array('%label' => $block_page->label()));
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $page_variant
   *   The page variant.
   *
   * @return string
   *   The title for the page variant edit form.
   */
  public function editPageVariantTitle(BlockPageInterface $block_page, $page_variant) {
    $page_variant = $block_page->getPageVariant($page_variant);
    return $this->t('Edit %label page variant', array('%label' => $page_variant->label()));
  }

}
