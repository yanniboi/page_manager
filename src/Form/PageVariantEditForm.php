<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantEditForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\page_manager\PageInterface;
use Drupal\Component\Serialization\Json;

/**
 * Provides a form for editing a page variant.
 */
class PageVariantEditForm extends PageVariantFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_page_variant_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Update page variant');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL) {
    $form = parent::buildForm($form, $form_state, $page, $page_variant_id);

    // Set up the attributes used by a modal to prevent duplication later.
    $attributes = array(
      'class' => array('use-ajax'),
      'data-accepts' => 'application/vnd.drupal-modal',
      'data-dialog-options' => Json::encode(array(
        'width' => 'auto',
      )),
    );
    $add_button_attributes = NestedArray::mergeDeep($attributes, array(
      'class' => array(
        'button',
        'button--small',
        'button-action',
      )
    ));

    if ($selection_conditions = $this->pageVariant->getSelectionConditions()) {
      // Selection conditions.
      $form['selection_section'] = array(
        '#type' => 'details',
        '#title' => $this->t('Selection Conditions'),
        '#open' => TRUE,
      );
      $form['selection_section']['add'] = array(
        '#type' => 'link',
        '#title' => $this->t('Add new selection condition'),
        '#route_name' => 'page_manager.selection_condition_select',
        '#route_parameters' => array(
          'page' => $this->page->id(),
          'page_variant_id' => $this->pageVariant->id(),
        ),
        '#attributes' => $add_button_attributes,
        '#attached' => array(
          'library' => array(
            'core/drupal.ajax',
          ),
        ),
      );
      $form['selection_section']['table'] = array(
        '#type' => 'table',
        '#header' => array(
          $this->t('Label'),
          $this->t('Description'),
          $this->t('Operations'),
        ),
        '#empty' => $this->t('There are no selection conditions.'),
      );

      $form['selection_section']['selection_logic'] = array(
        '#type' => 'radios',
        '#options' => array(
          'and' => $this->t('All conditions must pass'),
          'or' => $this->t('Only one condition must pass'),
        ),
        '#default_value' => $this->pageVariant->getSelectionLogic(),
      );

      $form['selection_section']['selection'] = array(
        '#tree' => TRUE,
      );
      foreach ($selection_conditions as $selection_id => $selection_condition) {
        $row = array();
        $row['label']['#markup'] = $selection_condition->getPluginDefinition()['label'];
        $row['description']['#markup'] = $selection_condition->summary();
        $operations = array();
        $operations['edit'] = array(
          'title' => $this->t('Edit'),
          'route_name' => 'page_manager.selection_condition_edit',
          'route_parameters' => array(
            'page' => $this->page->id(),
            'page_variant_id' => $this->pageVariant->id(),
            'condition_id' => $selection_id,
          ),
          'attributes' => $attributes,
        );
        $operations['delete'] = array(
          'title' => $this->t('Delete'),
          'route_name' => 'page_manager.selection_condition_delete',
          'route_parameters' => array(
            'page' => $this->page->id(),
            'page_variant_id' => $this->pageVariant->id(),
            'condition_id' => $selection_id,
          ),
          'attributes' => $attributes,
        );
        $row['operations'] = array(
          '#type' => 'operations',
          '#links' => $operations,
        );
        $form['selection_section']['table'][$selection_id] = $row;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // Save the page entity.
    $this->page->save();
    drupal_set_message($this->t('The %label page variant has been updated.', array('%label' => $this->pageVariant->label())));
    $form_state['redirect_route'] = $this->page->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  protected function preparePageVariant($page_variant_id) {
    // Load the page variant directly from the page entity.
    return $this->page->getPageVariant($page_variant_id)->init($this->page);
  }

}
