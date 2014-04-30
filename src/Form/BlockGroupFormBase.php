<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupFormBase.
 */

namespace Drupal\block_group\Form;

use Drupal\Core\Entity\EntityForm;

/**
 * @todo.
 */
abstract class BlockGroupFormBase extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#description' => $this->t('The label for this block group.'),
      '#default_value' => $this->entity->label(),
      '#maxlength' => '255',
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#disabled' => !$this->entity->isNew(),
      '#maxlength' => 64,
      '#required' => TRUE,
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
      ),
    );

    return parent::form($form, $form_state);
  }

  /**
   * Determines if the block group already exists.
   *
   * @param string $id
   *   The block group ID.
   *
   * @return bool
   *   TRUE if the format exists, FALSE otherwise.
   */
  public function exists($id) {
    return (bool) \Drupal::entityQuery('block_group')
      ->condition('id', $id)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $this->entity->save();
  }

}
