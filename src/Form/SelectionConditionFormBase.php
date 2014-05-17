<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\SelectionConditionFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;

/**
 * Provides a base form for editing and adding an selection condition.
 */
abstract class SelectionConditionFormBase extends FormBase {

  /**
   * The block page this selection condition belongs to.
   *
   * @var \Drupal\block_page\BlockPageInterface
   */
  protected $blockPage;

  /**
   * The page variant.
   *
   * @var \Drupal\block_page\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * The selection condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $selectionCondition;

  /**
   * Prepares the selection condition used by this form.
   *
   * @param string $selection_condition_id
   *   Either a selection condition ID, or the plugin ID used to create a new
   *   selection condition.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The selection condition object.
   */
  abstract protected function prepareSelectionCondition($selection_condition_id);

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
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant_id = NULL, $selection_condition_id = NULL) {
    $this->blockPage = $block_page;
    $this->pageVariant = $this->blockPage->getPageVariant($page_variant_id);
    $this->selectionCondition = $this->prepareSelectionCondition($selection_condition_id);

    // Allow the selection condition to add to the form.
    $form['plugin'] = $this->selectionCondition->buildConfigurationForm(array(), $form_state);
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
    // Allow the selection condition to validate the form.
    $plugin_values = array(
      'values' => &$form_state['values']['plugin'],
    );
    $this->selectionCondition->validateConfigurationForm($form, $plugin_values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Allow the selection condition to submit the form.
    $plugin_values = array(
      'values' => &$form_state['values']['plugin'],
    );
    $this->selectionCondition->submitConfigurationForm($form, $plugin_values);
    $form_state['redirect_route'] = new Url('block_page.page_variant_edit', array(
      'block_page' => $this->blockPage->id(),
      'page_variant_id' => $this->pageVariant->id(),
    ));
  }

}
