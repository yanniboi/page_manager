<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\VariantBag.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Core\Plugin\DefaultPluginBag;

/**
 * Provides a collection of variants plugins.
 */
class VariantBag extends DefaultPluginBag {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\Core\Display\VariantInterface
   */
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

  /**
   * {@inheritdoc}
   */
  public function sort() {
    // @todo Determine the reason this needs error suppression.
    @uasort($this->instanceIDs, array($this, 'sortHelper'));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function sortHelper($aID, $bID) {
    $a_weight = $this->get($aID)->getWeight();
    $b_weight = $this->get($bID)->getWeight();
    if ($a_weight == $b_weight) {
      return 0;
    }

    return ($a_weight < $b_weight) ? -1 : 1;
  }

}
