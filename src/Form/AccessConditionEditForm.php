<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\AccessConditionEditForm.
 */

namespace Drupal\block_page\Form;

/**
 * Provides a form for editing an access condition.
 */
class AccessConditionEditForm extends AccessConditionFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_access_condition_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareCondition($access_condition_id) {
    // Load the access condition directly from the block page.
    return $this->blockPage->getAccessCondition($access_condition_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Update access condition');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // Save the block page.
    $this->blockPage->save();
    drupal_set_message($this->t('The %label access condition has been updated.', array('%label' => $this->condition->getPluginDefinition()['label'])));
  }

}
