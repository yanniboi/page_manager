<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupEditBlockForm.
 */

namespace Drupal\block_group\Form;

use Drupal\block_group\BlockGroupInterface;

/**
 * Provides a form for editing a block plugin of a block group.
 */
class BlockGroupEditBlockForm extends BlockGroupConfigureBlockFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_group_edit_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockGroupInterface $block_group = NULL, $block_id = NULL) {
    $this->plugin = $block_group->getPluginBag()->get($block_id);

    $form = parent::buildForm($form, $form_state, $block_group, $block_id);

    $form['actions']['submit']['#value'] = $this->t('Update block');
    return $form;
  }

}
