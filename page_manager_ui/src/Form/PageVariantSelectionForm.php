<?php
/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageAccessForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ctools\Form\ManageConditions;

class PageVariantSelectionForm extends ManageConditions {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_access_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getConditionClass() {
    return 'Drupal\page_manager_ui\Form\SelectionConfigure';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTempstoreId() {
    return 'page_manager.page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getOperationsRouteInfo($cached_values, $machine_name, $row) {
    /** @var \Drupal\page_manager\PageVariantInterface $page_variant */
    $page_variant = $cached_values['page_variant'];
    return ['entity.page_variant.condition', [
      'machine_name' => $machine_name,
      'variant_machine_name' => $page_variant->id(),
      'condition' => $row
    ]];
  }

  /**
   * {@inheritdoc}
   */
  protected function getConditions($cached_values) {
    /** @var $page \Drupal\page_manager\Entity\PageVariant */
    $page_variant = $cached_values['page_variant'];
    return $page_variant->get('selection_criteria');
  }

  /**
   * {@inheritdoc}
   */
  protected function getContexts($cached_values) {
    /** @var $page \Drupal\page_manager\Entity\Page */
    $page = $cached_values['page'];
    return $page->getContexts();
  }

  /**
   * {@inheritdoc}
   */
  protected function getAddRoute($cached_values) {
    return 'entity.page_variant.condition.add';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if ($triggering_element['#value']->getUntranslatedString() != 'Add Condition') {
      return;
    }
    parent::submitForm($form, $form_state);
  }

}
