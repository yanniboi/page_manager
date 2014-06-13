<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantDeleteBlockForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\page_manager\PageInterface;

/**
 * Provides a form for deleting an access condition.
 */
class PageVariantDeleteBlockForm extends ConfirmFormBase {

  /**
   * The page entity.
   *
   * @var \Drupal\page_manager\PageInterface
   */
  protected $page;

  /**
   * The page variant.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * The plugin being configured.
   *
   * @var \Drupal\block\BlockPluginInterface
   */
  protected $block;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_page_variant_delete_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the block %label?', array('%label' => $this->block->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('page_manager.page_variant_edit', array(
      'page' => $this->page->id(),
      'page_variant_id' => $this->pageVariant->id()
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL, $block_id = NULL) {
    $this->page = $page;
    $this->pageVariant = $this->page->getVariant($page_variant_id);
    $this->block = $this->pageVariant->getBlock($block_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->pageVariant->removeBlock($this->block->getConfiguration()['uuid']);
    $this->page->save();
    drupal_set_message($this->t('The block %label has been removed.', array('%label' => $this->block->label())));

    $form_state['redirect_route'] = $this->getCancelRoute();
  }

}
