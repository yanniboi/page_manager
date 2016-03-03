<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageAddForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for adding a new page entity.
 */
class PageAddForm extends PageFormBase {

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $form['label']['#description'] = $this->t('The label for this mini panel.');

    return $form;
  }

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
