<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\AccessConditionFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Core\Form\FormBase;

/**
 * Provides a base form for editing and adding an access condition.
 */
abstract class AccessConditionFormBase extends FormBase {

  /**
   * The block page this access condition belongs to.
   *
   * @var \Drupal\block_page\BlockPageInterface
   */
  protected $blockPage;

  /**
   * The access condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $accessCondition;

  /**
   * Prepares the access condition used by this form.
   *
   * @param string $access_condition_id
   *   Either a access condition ID, or the plugin ID used to create a new
   *   access condition.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The access condition object.
   */
  abstract protected function prepareAccessCondition($access_condition_id);

  /**
   * Returns the text to use for the submit button.
   *
   * @return string
   *   The submit button text.
   */
  abstract protected function submitText();

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $access_condition_id = NULL) {
    $this->blockPage = $block_page;
    $this->accessCondition = $this->prepareAccessCondition($access_condition_id);

    // Allow the access condition to add to the form.
    $form['plugin'] = $this->accessCondition->buildForm(array(), $form_state);
    $form['plugin']['#tree'] = TRUE;

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->submitText(),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    // Allow the access condition to validate the form.
    $plugin_values = array(
      'values' => &$form_state['values']['plugin'],
    );
    $this->accessCondition->validateForm($form, $plugin_values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Allow the access condition to submit the form.
    $plugin_values = array(
      'values' => &$form_state['values']['plugin'],
    );
    $this->accessCondition->submitForm($form, $plugin_values);
  }

}
