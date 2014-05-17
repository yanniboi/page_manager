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
   * @param \Drupal\Core\Condition\ConditionInterface[] $conditions
   *   A set of conditions.
   * @param string $condition_logic
   *   The logic used to compute access, either 'and' or 'or'.
   *
   * @return bool
   *   Whether these conditions grant or deny access.
   */
  protected function resolveConditions($conditions, $condition_logic) {
    foreach ($conditions as $condition) {
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
        // If a condition is missing context and all conditions were required,
        // deny access.
        if ($condition_logic == 'and') {
          return FALSE;
        }
      }
    }

    // If no conditions passed and one condition was required, deny access.
    return $condition_logic == 'and';
  }

}
