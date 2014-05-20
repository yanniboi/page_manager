<?php

/**
 * @file
 * Contains \Drupal\block_page\ContextHandler.
 */

namespace Drupal\block_page;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TypedDataManager;

/**
 * Provides methods to handle sets of contexts.
 */
class ContextHandler {

  /**
   * The typed data manager.
   *
   * @var \Drupal\Core\TypedData\TypedDataManager
   */
  protected $typed_data;

  /**
   * Constructs a new ContextHandler.
   *
   * @param \Drupal\Core\TypedData\TypedDataManager $typed_data
   *   The typed data manager.
   */
  public function __construct(TypedDataManager $typed_data) {
    $this->typed_data = $typed_data;
  }

  /**
   * Checks a set of requirements against a set of contexts.
   *
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   An array of contexts.
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $requirements
   *   An array of requirements.
   *
   * @return bool
   *   TRUE if all of the requirements are satisfied by the context, FALSE
   *   otherwise.
   */
  public function checkRequirements(array $contexts, array $requirements) {
    $results = array();
    foreach ($requirements as $name => $requirement) {
      if ($requirement->isRequired()) {
        foreach ($contexts as $context) {
          // @todo getContextDefinition() should return a DataDefinitionInterface.
          $context_definition = new DataDefinition($context->getContextDefinition());
          if ($requirement->getDataType() == $context_definition->getDataType()) {
            foreach ($requirement->getConstraints() as $constraint_name => $constraint) {
              if ($context_definition->getConstraint($constraint_name) != $constraint) {
                continue 2;
              }
            }
          }
          $results[$name] = TRUE;
        }
      }
      else {
        $results[$name] = TRUE;
      }
    }
    return count($requirements) == count($results);
  }

  /**
   * Determines plugins whose constraints are satisfied by a set of contexts.
   *
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   An array of contexts.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $manager
   *   The plugin manager.
   *
   * @return array
   *   An array of plugin definitions.
   */
  public function getAvailablePlugins(array $contexts, PluginManagerInterface $manager) {
    $plugins = $manager->getDefinitions();
    $available_plugins = array();
    foreach ($plugins as $plugin_id => $plugin) {
      if (isset($plugin['context'])) {
        $plugin_contexts = $plugin['context'];
        $requirements = array();
        foreach ($plugin_contexts as $context_id => $plugin_context) {
          $definition = $this->typed_data->getDefinition($plugin_context['type']);
          $definition['type'] = $plugin_context['type'];
          if (isset($plugin_context['constraints'])) {
            if (!isset($definition['constraints'])) {
              $definition['constraints'] = $plugin_context['constraints'];
            }
            else {
              $definition['constraints'] += $plugin_context['constraints'];
            }
          }
          if (!isset($definition['required'])) {
            $definition['required'] = TRUE;
          }
          $requirements[$context_id] = new DataDefinition($definition);
        }
        if ($this->checkRequirements($contexts, $requirements)) {
          $available_plugins[$plugin_id] = $plugin;
        }
      }
      else {
        $available_plugins[$plugin_id] = $plugin;
      }
    }
    return $available_plugins;
  }

  /**
   * Determines which contexts satisfy the constraints of a given data definition.
   *
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   An array of contexts.
   * @param \Drupal\Core\TypedData\DataDefinitionInterface $definition
   *
   * @return \Drupal\Component\Plugin\Context\ContextInterface[]
   *   An array of valid contexts.
   */
  public function getValidContexts(array $contexts, DataDefinitionInterface $definition) {
    $valid = array();
    foreach ($contexts as $id => $context) {
      // @todo getContextDefinition() should return a DataDefinitionInterface.
      $context_definition = new DataDefinition($context->getContextDefinition());
      if ($definition->getDataType() == $context_definition->getDataType()) {
        foreach ($definition->getConstraints() as $constraint_name => $constraint) {
          if ($context_definition->getConstraint($constraint_name) != $constraint) {
            continue 2;
          }
        }
      }
      $valid[$id] = $context;
    }
    return $valid;
  }

}
