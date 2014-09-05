<?php

/**
 * @file
 * Contains \Drupal\Core\Plugin\ContextAwarePluginAssignmentTrait.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Handles context assignments for context-aware plugins.
 */
trait ContextAwarePluginAssignmentTrait {

  /**
   * Ensures the t() method is available.
   *
   * @see \Drupal\Core\StringTranslation\StringTranslationTrait
   */
  abstract protected function t($string, array $args = array(), array $options = array());

  /**
   * Wraps the context handler.
   *
   * @return \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected function contextHandler() {
    return \Drupal::service('context.handler');
  }

  /**
   * Builds a form element for assigning a context to a given slot.
   *
   * @param \Drupal\Component\Plugin\ContextAwarePluginInterface $plugin
   *   The context-aware plugin.
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   An array of contexts.
   *
   * @return array
   *   A form element for assigning context.
   */
  protected function addContextAssignmentElement(ContextAwarePluginInterface $plugin, $contexts) {
    $element = array();
    $element['#tree'] = TRUE;
    foreach ($plugin->getContextDefinitions() as $context_slot => $definition) {
      $valid_contexts = $this->contextHandler()->getMatchingContexts($contexts, $definition);
      $options = array();
      foreach ($valid_contexts as $context_id => $context) {
        $context_definition = $context->getContextDefinition();
        $options[$context_id] = $context_definition->getLabel();
      }

      $assignments = array();
      // @todo Find a better way to load context assignments.
      if ($plugin instanceof ConfigurablePluginInterface) {
        $configuration = $plugin->getConfiguration();
        if (isset($configuration['context_mapping'])) {
          $assignments = $configuration['context_mapping'];
        }
      }

      $element[$context_slot] = array(
        '#title' => $this->t('Select a @context value:', array('@context' => $context_slot)),
        '#type' => 'select',
        '#options' => $options,
        '#required' => $definition->isRequired(),
        '#default_value' => !empty($assignments[$context_slot]) ? $assignments[$context_slot] : '',
      );
    }
    return $element;
  }

  /**
   * Handles the submission for assigning a context to a given slot.
   *
   * @param \Drupal\Component\Plugin\ContextAwarePluginInterface $plugin
   *   The context-aware plugin.
   * @param array $assignments
   *   An array of assignments.
   */
  protected function submitContextAssignment(ContextAwarePluginInterface $plugin, $assignments) {
    if ($plugin instanceof ConfigurablePluginInterface) {
      $configuration = $plugin->getConfiguration();
      $configuration['context_mapping'] = $assignments;
      $plugin->setConfiguration($configuration);
    }
    else {
      // @todo Find a way to save context assignments for non-configurable plugins.
    }
  }

}
