<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\PageVariant\DefaultPageVariant.
 */

namespace Drupal\block_page\Plugin\PageVariant;

use Drupal\block_page\Plugin\PageVariantBase;

/**
 * Provides a default page variant.
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
    // @todo Reference an external object of some kind, like a Layout.
    return array(
      'top' => 'Top',
      'bottom' => 'Bottom',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access() {
    // @todo Develop something more sophisticated.
    return (bool) $this->getBlockCount();
  }

}
