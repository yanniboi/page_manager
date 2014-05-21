<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantEditBlockForm.
 */

namespace Drupal\page_manager\Form;

/**
 * Provides a form for editing a block plugin of a page variant.
 */
class PageVariantEditBlockForm extends PageVariantConfigureBlockFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_page_variant_edit_block_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareBlock($block_id) {
    return $this->pageVariant->getBlock($block_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Update block');
  }

}
