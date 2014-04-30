<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupConfigureBlockFormBase.
 */

namespace Drupal\block_group\Form;

use Drupal\block_group\BlockGroupInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;

/**
 * Provides a base form for configuring a block as part of a block group.
 */
abstract class BlockGroupConfigureBlockFormBase extends FormBase {

  /**
   * The block group.
   *
   * @var \Drupal\block_group\BlockGroupInterface
   */
  protected $entity;

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
  public function buildForm(array $form, array &$form_state, BlockGroupInterface $block_group = NULL, $block_id = NULL) {
    $this->entity = $block_group;
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
      '#options' => $this->entity->getRegionNames(),
      '#default_value' => $this->entity->getRegionAssignment($this->blockId),
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
    $this->entity->setRegionAssignment($this->blockId, $form_state['values']['region']);
    $this->entity->save();

    $form_state['redirect_route'] = new Url('block_group.edit', array(
      'block_group' => $this->entity->id(),
    ));
  }

}
