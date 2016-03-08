<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageFormTrait
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Trait to add additional Page related functionality to entity forms.
 *
 * Designed for use on forms extending \Drupal\ctools\Form\DisplayFormBase.
 */
trait PageFormTrait {

  /**
   * Add page specific items to the form.
   *
   * @see \Drupal\ctools\Form\DisplayFormBase::form()
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Make some weight adjustments to get our element in the right place.
    $form['label']['#weight'] = -2;
    $form['id']['#weight'] = -2;

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->getPath(),
      '#required' => TRUE,
      '#element_validate' => [[$this, 'validatePath']],
      '#weight' => -1,
    ];

    return $form;
  }

  /**
   * Element validation callback for the path element.
   */
  public function validatePath(array &$element, FormStateInterface $form_state) {
    // Ensure the path has a leading slash.
    $value = '/' . trim($element['#value'], '/');
    $form_state->setValueForElement($element, $value);
    // Ensure each path is unique.
    $path = $this->entityQuery->get('page')
      ->condition('path', $value)
      ->condition('id', $form_state->getValue('id'), '<>')
      ->execute();
    if ($path) {
      $form_state->setErrorByName('path', $this->t('The page path must be unique.'));
    }
  }

}
