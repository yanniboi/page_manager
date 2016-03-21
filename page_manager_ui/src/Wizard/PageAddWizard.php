<?php
/**
 * @file
 * Contains \Drupal\page_manager_ui\Wizard\PageAddWizard.
 */

namespace Drupal\page_manager_ui\Wizard;

use Drupal\Core\Display\ContextAwareVariantInterface;
use Drupal\ctools\Plugin\PluginWizardInterface;

class PageAddWizard extends PageWizardBase {

  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    return 'entity.page.add_step_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations($cached_values) {
    $operations = parent::getOperations($cached_values);

    // Add steps for selection and creating the first variant.
    $operations['contexts'] = [
      'title' => $this->t('Contexts'),
      'form' => '\Drupal\page_manager_ui\Form\PageVariantContextsForm',
    ];
    $operations['selection'] = [
      'title' => $this->t('Selection criteria'),
      'form' => '\Drupal\page_manager_ui\Form\PageVariantSelectionForm',
    ];
    $operations['display_variant'] = [
      'title' => $this->t('Configure variant'),
      'form' => '\Drupal\page_manager_ui\Form\PageVariantConfigureForm',
    ];

    // Hide any optional steps that aren't selected.
    $optional_steps = ['parameters', 'access', 'contexts', 'selection'];
    foreach ($optional_steps as $step_name) {
      if (empty($cached_values['wizard_options'][$step_name])) {
        unset($operations[$step_name]);
      }
    }

    // Add any wizard operations from the plugin itself.
    if (!empty($cached_values['page_variant'])) {
      /** @var \Drupal\page_manager\PageVariantInterface $page_variant */
      $page_variant = $cached_values['page_variant'];
      $variant_plugin = $page_variant->getVariantPlugin();
      if ($variant_plugin instanceof PluginWizardInterface) {
        if ($variant_plugin instanceof ContextAwareVariantInterface) {
          $variant_plugin->setContexts($page_variant->getContexts());
        }
        $cached_values['plugin'] = $variant_plugin;
        foreach ($variant_plugin->getWizardOperations($cached_values) as $name => $operation) {
          $operation['values']['plugin'] = $variant_plugin;
          $operation['submit'][] = '::submitVariantStep';
          $operations[$name] = $operation;
        }
      }
    }

    return $operations;
  }

}
