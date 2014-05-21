<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\AccessConditionFormBase.
 */

namespace Drupal\page_manager\Form;

/**
 * Provides a base form for editing and adding an access condition.
 */
abstract class AccessConditionFormBase extends ConditionFormBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $configuration = $this->condition->getConfiguration();
    // If this access condition is new, add it to the page.
    if (!isset($configuration['uuid'])) {
      $this->page->addAccessCondition($configuration);
    }

    // Save the page entity.
    $this->page->save();

    $form_state['redirect_route'] = $this->page->urlInfo('edit-form');
  }

}
