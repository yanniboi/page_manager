<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\ConditionAccessResolverTrait.
 */

namespace Drupal\block_page\Plugin;

use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Resolves a set of conditions.
 */
trait ConditionAccessResolverTrait {

  /**
   * Resolves the given conditions based on the condition logic ('and'/'or').
   *
   * @param \Drupal\block_page\Plugin\ConditionPluginBag $condition_plugin_bag
   *   The condition plugin bag.
   * @param string $condition_logic
   *   The logic used to compute access, either 'and' or 'or'.
   *
   * @return bool
   *   Whether these conditions grant or deny access.
   */
  protected function resolveConditions(ConditionPluginBag $condition_plugin_bag, $condition_logic) {
    foreach ($condition_plugin_bag as $condition) {
      try {
        $pass = $condition->execute();
        // If a condition fails and all conditions were required, deny access.
        if (!$pass && $condition_logic == 'and') {
          return FALSE;
        }
        // If a condition passes and one condition was required, grant access.
        elseif ($pass && $condition_logic == 'or') {
          return TRUE;
        }
      }
      catch (PluginException $e) {
        // A missing context should always deny access.
        return FALSE;
      }
    }
    return TRUE;
  }

}
