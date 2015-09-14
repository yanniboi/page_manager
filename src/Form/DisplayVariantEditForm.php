<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\DisplayVariantEditForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\page_manager\PageInterface;
use Drupal\page_manager\Plugin\ConditionVariantInterface;

/**
 * Provides a form for editing a display variant.
 */
class DisplayVariantEditForm extends DisplayVariantFormBase {

  use AjaxFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_display_variant_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Update display variant');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, PageInterface $page = NULL, $display_variant_id = NULL) {
    $form = parent::buildForm($form, $form_state, $page, $display_variant_id);

    if ($this->displayVariant instanceof ConditionVariantInterface) {
      $form['selection_section'] = $this->buildSelectionForm();
    }

    return $form;
  }

  /**
   * Builds the selection form for a variant.
   *
   * @return array
   */
  protected function buildSelectionForm() {
    // Set up the attributes used by a modal to prevent duplication later.
    $attributes = $this->getAjaxAttributes();
    $add_button_attributes = $this->getAjaxButtonAttributes();

    $form = [];
    if ($this->displayVariant instanceof ConditionVariantInterface) {
      if ($selection_conditions = $this->displayVariant->getSelectionConditions()) {
        // Selection conditions.
        $form = [
          '#type' => 'details',
          '#title' => $this->t('Selection Conditions'),
          '#open' => TRUE,
        ];
        $form['add'] = [
          '#type' => 'link',
          '#title' => $this->t('Add new selection condition'),
          '#url' => Url::fromRoute('page_manager.selection_condition_select', [
            'page' => $this->page->id(),
            'display_variant_id' => $this->displayVariant->id(),
          ]),
          '#attributes' => $add_button_attributes,
          '#attached' => [
            'library' => [
              'core/drupal.ajax',
            ],
          ],
        ];
        $form['table'] = [
          '#type' => 'table',
          '#header' => [
            $this->t('Label'),
            $this->t('Description'),
            $this->t('Operations'),
          ],
          '#empty' => $this->t('There are no selection conditions.'),
        ];

        $form['selection_logic'] = [
          '#type' => 'radios',
          '#options' => [
            'and' => $this->t('All conditions must pass'),
            'or' => $this->t('Only one condition must pass'),
          ],
          '#default_value' => $this->displayVariant->getSelectionLogic(),
        ];

        $form['selection'] = [
          '#tree' => TRUE,
        ];
        foreach ($selection_conditions as $selection_id => $selection_condition) {
          $row = [];
          $row['label']['#markup'] = $selection_condition->getPluginDefinition()['label'];
          $row['description']['#markup'] = $selection_condition->summary();
          $operations = [];
          $operations['edit'] = [
            'title' => $this->t('Edit'),
            'url' => Url::fromRoute('page_manager.selection_condition_edit', [
              'page' => $this->page->id(),
              'display_variant_id' => $this->displayVariant->id(),
              'condition_id' => $selection_id,
            ]),
            'attributes' => $attributes,
          ];
          $operations['delete'] = [
            'title' => $this->t('Delete'),
            'url' => Url::fromRoute('page_manager.selection_condition_delete', [
              'page' => $this->page->id(),
              'display_variant_id' => $this->displayVariant->id(),
              'condition_id' => $selection_id,
            ]),
            'attributes' => $attributes,
          ];
          $row['operations'] = [
            '#type' => 'operations',
            '#links' => $operations,
          ];
          $form['table'][$selection_id] = $row;
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the page entity.
    $this->page->save();
    drupal_set_message($this->t('The %label display variant has been updated.', ['%label' => $this->displayVariant->label()]));
    $form_state->setRedirectUrl($this->page->urlInfo('edit-form'));
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareDisplayVariant($display_variant_id) {
    // Load the display variant directly from the page entity.
    return $this->page->getVariant($display_variant_id);
  }

}
