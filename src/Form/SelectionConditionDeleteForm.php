<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\SelectionConditionDeleteForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\page_manager\PageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * @todo.
 */
class SelectionConditionDeleteForm extends ConfirmFormBase {

  /**
   * The page entity this selection condition belongs to.
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
   * The selection condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $selectionCondition;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_selection_condition_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the selection condition %name?', array('%name' => $this->selectionCondition->getPluginDefinition()['label']));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('page_manager.page_variant_edit', array(
      'page' => $this->page->id(),
      'page_variant_id' => $this->pageVariant->id(),
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
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL, $condition_id = NULL) {
    $this->page = $page;
    $this->pageVariant = $this->page->getVariant($page_variant_id);
    $this->selectionCondition = $this->pageVariant->getSelectionCondition($condition_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->pageVariant->removeSelectionCondition($this->selectionCondition->getConfiguration()['uuid']);
    $this->page->save();
    drupal_set_message($this->t('The selection condition %name has been removed.', array('%name' => $this->selectionCondition->getPluginDefinition()['label'])));
    $form_state['redirect_route'] = $this->getCancelRoute();
  }

}
