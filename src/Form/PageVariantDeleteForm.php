<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantDeleteForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\page_manager\PageInterface;
use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a form for deleting a page variant.
 */
class PageVariantDeleteForm extends ConfirmFormBase {

  /**
   * The page entity this page variant belongs to.
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_page_variant_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the page variant %name?', array('%name' => $this->pageVariant->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return $this->page->urlInfo('edit-form');
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
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL) {
    $this->page = $page;
    $this->pageVariant = $page->getVariant($page_variant_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->page->removeVariant($this->pageVariant->id());
    $this->page->save();
    drupal_set_message($this->t('The page variant %name has been removed.', array('%name' => $this->pageVariant->label())));
    $form_state['redirect_route'] = $this->getCancelRoute();
  }

}
