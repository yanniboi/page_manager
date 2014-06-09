<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\ConditionAccessResolverTrait.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Resolves a set of conditions.
 */
trait ConditionAccessResolverTrait {

  /**
   * Wraps the context handler.
   *
   * @return \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected function contextHandler() {
    return \Drupal::service('context.handler');
  }

  /**
   * Resolves the given conditions based on the condition logic ('and'/'or').
   *
   * @param \Drupal\Core\Condition\ConditionInterface[] $conditions
   *   A set of conditions.
   * @param string $condition_logic
   *   The logic used to compute access, either 'and' or 'or'.
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   (optional) An array of contexts to set on the conditions.
   *
   * @return bool
   *   Whether these conditions grant or deny access.
   */
  protected function resolveConditions($conditions, $condition_logic, $contexts = array()) {
    foreach ($conditions as $condition) {
      if ($condition instanceof ContextAwarePluginInterface) {
        $assignments = array();
        if ($condition instanceof ConfigurablePluginInterface) {
          $configuration = $condition->getConfiguration();
          if (isset($configuration['context_assignments'])) {
            $assignments = array_flip($configuration['context_assignments']);
          }
        }
        $this->contextHandler()->applyContextMapping($condition, $contexts, $assignments);
      }

      try {
        $pass = $condition->execute();
      }
      catch (PluginException $e) {
        // If a condition is missing context, consider that a fail.
        $pass = FALSE;
      }

      // If a condition fails and all conditions were required, deny access.
      if (!$pass && $condition_logic == 'and') {
        return FALSE;
      }
      // If a condition passes and one condition was required, grant access.
      elseif ($pass && $condition_logic == 'or') {
        return TRUE;
      }
    }

    // If no conditions passed and one condition was required, deny access.
    return $condition_logic == 'and';
  }

}
