<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\PageVariant\DefaultPageVariant.
 */

namespace Drupal\block_page\Plugin\PageVariant;

use Drupal\block_page\Plugin\PageVariantBase;

/**
 * @todo.
 *
 * @PageVariant(
 *   id = "default"
 * )
 */
class DefaultPageVariant extends PageVariantBase {

  /**
   * {@inheritdoc}
   */
  public function getRegionNames() {
    return array(
      'top' => 'Top',
      'bottom' => 'Bottom',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access() {
    // @todo.
    return (bool) $this->getBlockCount();
  }

}
