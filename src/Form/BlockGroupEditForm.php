<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupEditForm.
 */

namespace Drupal\block_group\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\String;
use Drupal\Core\Url;

/**
 * Provides a form for editing a block group.
 */
class BlockGroupEditForm extends BlockGroupFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $attributes = array(
      'class' => array('use-ajax'),
      'data-accepts' => 'application/vnd.drupal-modal',
      'data-dialog-options' => Json::encode(array(
        'width' => 'auto',
      )),
    );
    $form['blocks'] = array(
      '#prefix' => '<h3>' . $this->t('Blocks') . '</h3>',
      '#type' => 'table',
      '#header' => array($this->t('Label'), $this->t('Plugin ID'), $this->t('Region'), $this->t('Weight'), $this->t('Operations')),
      '#empty' => $this->t('There are no regions for blocks.')
    );
    foreach ($this->entity->getRegionAssignments() as $region => $blocks) {
      $form['blocks']['#tabledrag'][] = array(
        'action' => 'match',
        'relationship' => 'sibling',
        'group' => 'block-region-select',
        'subgroup' => 'block-region-' . $region,
        'hidden' => FALSE,
      );
      $form['blocks']['#tabledrag'][] = array(
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'block-weight',
        'subgroup' => 'block-weight-' . $region,
      );
      $form['blocks'][$region] = array(
        '#attributes' => array(
          'class' => array('region-title', 'region-title-' . $region),
          'no_striping' => TRUE,
        ),
      );
      $form['blocks'][$region]['title'] = array(
        '#markup' => $this->entity->getRegionName($region),
        '#wrapper_attributes' => array(
          'colspan' => 5,
        ),
      );
      $form['blocks'][$region . '-message'] = array(
        '#attributes' => array(
          'class' => array(
            'region-message',
            'region-' . $region . '-message',
            empty($blocks) ? 'region-empty' : 'region-populated',
          ),
        ),
      );
      $form['blocks'][$region . '-message']['message'] = array(
        '#markup' => '<em>' . t('No blocks in this region') . '</em>',
        '#wrapper_attributes' => array(
          'colspan' => 5,
        ),
      );

      $weight_delta = round($this->entity->getBlockCount() / 2);
      foreach ($blocks as $block_id => $block) {
        $row = array(
          '#attributes' => array(
            'class' => array('draggable'),
          ),
        );
        /** @var $block \Drupal\block\BlockPluginInterface */
        $row['label']['#markup'] = $block->label();
        $row['id']['#markup'] = $block->getPluginId();
        $row['region'] = array(
          '#title' => $this->t('Region'),
          '#title_display' => 'invisible',
          '#type' => 'select',
          '#options' => $this->entity->getRegionNames(),
          '#default_value' => $this->entity->getRegionAssignment($block_id),
          '#attributes' => array(
            'class' => array('block-region-select', 'block-region-' . $region),
          ),
        );
        $operations = array();
        $operations['edit'] = array(
          'title' => $this->t('Edit'),
          'route_name' => 'block_group.edit_block',
          'route_parameters' => array(
            'block_group' => $this->entity->id(),
            'block_id' => $block_id,
          ),
          'attributes' => $attributes,
        );
        $configuration = $block->getConfiguration();
        $row['weight'] = array(
          '#type' => 'weight',
          '#default_value' => isset($configuration['weight']) ? $configuration['weight'] : 0,
          '#delta' => $weight_delta,
          '#title' => t('Weight for @block block', array('@block' => $block->label())),
          '#title_display' => 'invisible',
          '#attributes' => array(
            'class' => array('block-weight', 'block-weight-' . $region),
          ),
        );
        $row['operations'] = array(
          '#type' => 'operations',
          '#links' => $operations,
        );
        $form['blocks'][$block_id] = $row;
      }
    }
    $form['available_blocks'] = array(
      '#type' => 'details',
      '#title' => $this->t('Available blocks'),
      '#attached' => array(
        'library' => array(
          'core/drupal.ajax',
        ),
      ),
    );
    $plugins = $this->blockManager->getSortedDefinitions();
    foreach ($plugins as $plugin_id => $plugin_definition) {
      $category = String::checkPlain($plugin_definition['category']);
      $category_key = 'category-' . $category;
      if (!isset($form['available_blocks'][$category_key])) {
        $form['available_blocks'][$category_key] = array(
          '#type' => 'fieldgroup',
          '#title' => $category,
          'content' => array(
            '#theme' => 'links',
          ),
        );
      }
      $form['available_blocks'][$category_key]['content']['#links'][$plugin_id] = array(
        'title' => $plugin_definition['admin_label'],
        'route_name' => 'block_group.add_block',
        'route_parameters' => array(
          'block_group' => $this->entity->id(),
          'plugin_id' => $plugin_id,
        ),
        'attributes' => $attributes,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    foreach ($form_state['values']['blocks'] as $block_id => $block_values) {
      $this->entity->setRegionAssignment($block_id, $block_values['region']);
    };
    parent::save($form, $form_state);
    drupal_set_message($this->t('The %label block group has been updated.', array('%label' => $this->entity->label())));
    $form_state['redirect_route'] = new Url('block_group.list');
  }

}
