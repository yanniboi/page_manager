<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageAddForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ctools\Form\DisplayFormBase;

/**
 * Provides a form for adding a new page entity.
 */
class PageAddForm extends \Drupal\ctools\Form\DisplayFormBase {

  use PageFormTrait;

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    drupal_set_message($this->t('The %label page has been added.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.page.edit_form', [
      'page' => $this->entity->id(),
    ]);
  }

}
