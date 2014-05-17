<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\PageVariant\DefaultPageVariant.
 */

namespace Drupal\block_page\Plugin\PageVariant;

use Drupal\block_page\Plugin\ConditionAccessResolverTrait;
use Drupal\block_page\Plugin\PageVariantBase;

/**
 * Provides a default page variant.
 *
 * @PageVariant(
 *   id = "default"
 * )
 */
class DefaultPageVariant extends PageVariantBase {

  use ConditionAccessResolverTrait;

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
    return $this->resolveConditions($this->getSelectionConditions(), $this->getSelectionLogic());
  }

}
