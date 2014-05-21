<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\AccessConditionFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Form\FormBase;

/**
 * Provides a base form for editing and adding an access condition.
 */
abstract class AccessConditionFormBase extends ConditionFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $access_condition_id = NULL) {
    return parent::buildForm($form, $form_state, $block_page, $access_condition_id);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $form_state['redirect_route'] = $this->blockPage->urlInfo('edit-form');
  }

}
