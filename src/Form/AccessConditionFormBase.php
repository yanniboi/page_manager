<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\AccessConditionFormBase.
 */

namespace Drupal\block_page\Form;

/**
 * Provides a base form for editing and adding an access condition.
 */
abstract class AccessConditionFormBase extends ConditionFormBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $form_state['redirect_route'] = $this->blockPage->urlInfo('edit-form');
  }

}
