<?php
/**
 * @file
 * Contains \Drupal\page_manager_ui\Wizard\PageWizardBase.
 */

namespace Drupal\page_manager_ui\Wizard;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ctools\Wizard\EntityFormWizardBase;
use Drupal\page_manager\Access\PageManagerPluginAccess;

class PageWizardBase extends EntityFormWizardBase {

  public function initValues() {
    $cached_values = parent::initValues();
    $cached_values['access'] = new PageManagerPluginAccess();
    return $cached_values;
  }


  /**
   * {@inheritdoc}
   */
  public function getEntityType() {
    return 'page';
  }

  /**
   * {@inheritdoc}
   */
  public function exists() {
    return '\Drupal\page_manager\Entity\Page::load';
  }

  /**
   * {@inheritdoc}
   */
  public function getWizardLabel() {
    return $this->t('Page Manager');
  }

  /**
   * {@inheritdoc}
   */
  public function getMachineLabel() {
    return $this->t('Administrative title');
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations($cached_values) {
    $operations = [];
    $operations['general'] = [
      'title' => $this->t('Page Information'),
      'form' => '\Drupal\page_manager_ui\Form\PageGeneralForm',
    ];
    $operations['access'] = [
      'title' => $this->t('Page Access'),
      'form' => '\Drupal\page_manager_ui\Form\PageAccessForm',
    ];
    $operations['parameters'] = [
      'title' => $this->t('Page Parameters'),
      'form' => '\Drupal\page_manager_ui\Form\PageParametersForm',
    ];

    return $operations;
  }

  /**
   * Submission callback for the variant plugin steps.
   */
  public function submitVariantStep(array &$form, FormStateInterface $form_state) {
    $cached_values = $form_state->getTemporaryValue('wizard');
    /** @var \Drupal\page_manager\PageVariantInterface $page_variant */
    $page_variant = $cached_values['page_variant'];
    /** @var \Drupal\Core\Display\VariantInterface $plugin */
    $plugin = $cached_values['plugin'];

    // Make sure the variant plugin on the page variant gets the configuration
    // from the 'plugin' which should have been setup by the variant's steps.
    if (!empty($plugin) && !empty($page_variant)) {
      $page_variant->getVariantPlugin()->setConfiguration($plugin->getConfiguration());
    }
  }

  public function finish(array &$form, FormStateInterface $form_state) {
    parent::finish($form, $form_state);

    $cached_values = $form_state->getTemporaryValue('wizard');
    /** @var \Drupal\page_manager\Entity\Page $page */
    $page = $cached_values['page'];
    foreach($page->getVariants() as $variant) {
      $variant->save();
    }

    $form_state->setRedirectUrl(new Url('entity.page.edit_form', [
      'machine_name' => $this->machine_name,
      'step' => $this->step
    ]));
  }

}
