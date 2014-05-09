<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantEditBlockForm.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;

/**
 * Provides a form for editing a block plugin of a page variant.
 */
class PageVariantEditBlockForm extends PageVariantConfigureBlockFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_page_variant_edit_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant = NULL, $block_id = NULL) {
    $this->blockPage = $block_page;
    $this->pageVariant = $block_page->getPageVariant($page_variant);
    $this->plugin = $this->pageVariant->getBlock($block_id);

    $form = parent::buildForm($form, $form_state, $block_id);

    $form['actions']['submit']['#value'] = $this->t('Update block');
    return $form;
  }

}
