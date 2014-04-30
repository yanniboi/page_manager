<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupAddForm.
 */

namespace Drupal\block_group\Form;

use Drupal\Core\Url;

/**
 * Provides a form for adding a new block group.
 */
class BlockGroupAddForm extends BlockGroupFormBase {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    parent::save($form, $form_state);
    drupal_set_message($this->t('The %label block group has been added.', array('%label' => $this->entity->label())));
    $form_state['redirect_route'] = new Url('block_group.edit', array(
      'block_group' => $this->entity->id(),
    ));
  }

}
