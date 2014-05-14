<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Core\Form\FormBase;

/**
 * Provides a base form for editing and adding a page variant.
 */
abstract class PageVariantFormBase extends FormBase {

  /**
   * @var \Drupal\block_page\BlockPageInterface
   */
  protected $blockPage;

  /**
   * @var \Drupal\block_page\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * @param string $page_variant
   *
   * @return \Drupal\block_page\Plugin\PageVariantInterface
   */
  abstract protected function preparePageVariant($page_variant);

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant = NULL) {
    $this->blockPage = $block_page;
    $this->pageVariant = $this->preparePageVariant($page_variant);
    $form['plugin'] = $this->pageVariant->buildConfigurationForm(array(), $form_state);
    $form['plugin']['#tree'] = TRUE;

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    $plugin_values = array(
      'values' => &$form_state['values']['plugin']
    );
    $form['plugin'] = $this->pageVariant->validateConfigurationForm($form, $plugin_values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $plugin_values = array(
      'values' => &$form_state['values']['plugin']
    );
    $this->pageVariant->submitConfigurationForm($form, $plugin_values);

    if (!$this->pageVariant->id()) {
      // Save the page variant and update the instance.
      $this->pageVariant = $this->blockPage->addPageVariant($this->pageVariant->getConfiguration());
    }
    $this->blockPage->save();
  }

}
