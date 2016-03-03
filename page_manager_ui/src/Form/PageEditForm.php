<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageEditForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ctools\Form\AjaxFormTrait;
use Drupal\panels\Form\DisplayEditForm;

/**
 * Provides a form for editing a page entity.
 */
class PageEditForm extends DisplayEditForm {

  use AjaxFormTrait;

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['use_admin_theme'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use admin theme'),
      '#default_value' => $this->entity->usesAdminTheme(),
      '#weight' => 0,
    ];

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->getPath(),
      '#required' => TRUE,
      '#element_validate' => [[$this, 'validatePath']],
      '#weight' => 0,
    ];

    $form['variant_section']['add_new_page']['#url'] = Url::fromRoute('entity.page.variant_select', [
      'page' => $this->entity->id(),
    ]);

    return $form;
  }

  /**
   * Builds the parameters form for a page entity.
   *
   * @return array
   */
  protected function buildParametersForm($add_button_attributes) {
    $form = parent::buildParametersForm($add_button_attributes);
    unset($form['add_new_parameter']);

    foreach ($this->entity->getParameterNames() as $parameter_name) {
      $parameter = $this->entity->getParameter($parameter_name);
      $row = [];
      $row['machine_name'] = $parameter['machine_name'];
      if ($label = $parameter['label']) {
        $row['label'] = $label;
      }
      else {
        $row['type']['colspan'] = 2;
      }
      $row['type']['data'] = $parameter['type'] ?: $this->t('<em>No context assigned</em>');

      $operations = [];
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'url' => Url::fromRoute('entity.page.parameter_edit', [
          'page' => $this->entity->id(),
          'name' => $parameter['machine_name'],
        ]),
        'attributes' => $this->getAjaxAttributes(),
      ];
      $row['operations']['data'] = [
        '#type' => 'operations',
        '#links' => $operations,
      ];

      $form['parameters']['#rows'][$parameter['machine_name']] = $row;
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    if (!$form_state->isValueEmpty('variants')) {
      foreach ($form_state->getValue('variants') as $variant_id => $data) {
        if ($variant_entity = $this->entity->getVariant($variant_id)) {
          $variant_entity->setWeight($data['weight']);
          $variant_entity->save();
        }
      }
    }
    parent::save($form, $form_state);
    drupal_set_message($this->t('The %label page has been updated.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.page.collection');
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $keys_to_ignore = ['variants', 'parameters'];
    $values_to_restore = [];
    foreach ($keys_to_ignore as $key) {
      $values_to_restore[$key] = $form_state->getValue($key);
      $form_state->unsetValue($key);
    }
    parent::copyFormValuesToEntity($entity, $form, $form_state);
    foreach ($values_to_restore as $key => $value) {
      $form_state->setValue($key, $value);
    }
  }

}
