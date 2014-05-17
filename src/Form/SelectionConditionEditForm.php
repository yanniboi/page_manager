<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\SelectionConditionEditForm.
 */

namespace Drupal\block_page\Form;

/**
 * Provides a form for editing an selection condition.
 */
class SelectionConditionEditForm extends SelectionConditionFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_selection_condition_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareSelectionCondition($selection_condition_id) {
    // Load the selection condition directly from the page variant.
    return $this->pageVariant->getSelectionCondition($selection_condition_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Update selection condition');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // Save the block page.
    $this->blockPage->save();
    drupal_set_message($this->t('The %label selection condition has been updated.', array('%label' => $this->selectionCondition->getPluginDefinition()['label'])));
  }

}
