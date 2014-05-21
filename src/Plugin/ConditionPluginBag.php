<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\ConditionPluginBag.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Core\Plugin\DefaultPluginBag;

/**
 * Provides a collection of condition plugins.
 */
class ConditionPluginBag extends DefaultPluginBag {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   */
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

}
