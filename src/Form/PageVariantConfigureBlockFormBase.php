<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantConfigureBlockFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;

/**
 * Provides a base form for configuring a block as part of a page variant.
 */
abstract class PageVariantConfigureBlockFormBase extends FormBase {

  /**
   * @var \Drupal\block_page\BlockPageInterface
   */
  protected $blockPage;

  /**
   * @var \Drupal\block_page\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * The plugin being configured.
   *
   * @var \Drupal\block\BlockPluginInterface
   */
  protected $plugin;

  /**
   * The ID of the block being configured.
   *
   * @var string
   */
  protected $blockId;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $block_id = NULL) {
    $this->blockId = $block_id;

    $form['#tree'] = TRUE;
    $form['settings'] = $this->plugin->buildConfigurationForm(array(), $form_state);
    $form['settings']['id'] = array(
      '#type' => 'value',
      '#value' => $this->plugin->getPluginId(),
    );
    $form['region'] = array(
      '#title' => $this->t('Region'),
      '#type' => 'select',
      '#options' => $this->pageVariant->getRegionNames(),
      '#default_value' => $this->pageVariant->getRegionAssignment($this->blockId),
      '#required' => TRUE,
    );

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save block'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    $settings = array(
      'values' => &$form_state['values']['settings']
    );
    // Call the plugin validate handler.
    $this->plugin->validateConfigurationForm($form, $settings);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $settings = array(
      'values' => &$form_state['values']['settings'],
      'errors' => $form_state['errors'],
    );

    // Call the plugin submit handler.
    $this->plugin->submitConfigurationForm($form, $settings);
    $this->pageVariant->updateBlock($this->blockId, array('region' => $form_state['values']['region']));
    $this->blockPage->save();

    $form_state['redirect_route'] = new Url('block_page.page_variant_edit', array(
      'block_page' => $this->blockPage->id(),
      'page_variant' => $this->pageVariant->id(),
    ));
  }

}
