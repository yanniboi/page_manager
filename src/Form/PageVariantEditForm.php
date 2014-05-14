<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantEditForm.
 */

namespace Drupal\block_page\Form;

use Drupal\block\BlockManagerInterface;
use Drupal\block_page\BlockPageInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\String;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for editing a page variant.
 */
class PageVariantEditForm extends PageVariantFormBase {

  /**
   * The block manager.
   *
   * @var \Drupal\block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs a new PageVariantEditForm.
   *
   * @param \Drupal\block\BlockManagerInterface $block_manager
   *   The block manager.
   */
  public function __construct(BlockManagerInterface $block_manager) {
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_page_variant_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant_id = NULL) {
    $form = parent::buildForm($form, $form_state, $block_page, $page_variant_id);

    $form['actions']['submit']['#value'] = $this->t('Update page variant');

    // Set up the attributes used by a modal to prevent duplication later.
    $attributes = array(
      'class' => array('use-ajax'),
      'data-accepts' => 'application/vnd.drupal-modal',
      'data-dialog-options' => Json::encode(array(
        'width' => 'auto',
      )),
    );

    // Build a table of all blocks used by this page variant.
    $form['blocks'] = array(
      '#prefix' => '<h3>' . $this->t('Blocks') . '</h3>',
      '#type' => 'table',
      '#header' => array($this->t('Label'), $this->t('Plugin ID'), $this->t('Region'), $this->t('Weight'), $this->t('Operations')),
      '#empty' => $this->t('There are no regions for blocks.')
    );
    // Loop through the blocks per region.
    foreach ($this->pageVariant->getRegionAssignments() as $region => $blocks) {
      // Add a section for each region and allow blocks to be dragged between them.
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
        '#markup' => $this->pageVariant->getRegionName($region),
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

      /** @var $blocks \Drupal\block\BlockPluginInterface[] */
      foreach ($blocks as $block_id => $block) {
        $row = array(
          '#attributes' => array(
            'class' => array('draggable'),
          ),
        );
        $row['label']['#markup'] = $block->label();
        $row['id']['#markup'] = $block->getPluginId();
        // Allow the region to be changed for each block.
        $row['region'] = array(
          '#title' => $this->t('Region'),
          '#title_display' => 'invisible',
          '#type' => 'select',
          '#options' => $this->pageVariant->getRegionNames(),
          '#default_value' => $this->pageVariant->getRegionAssignment($block_id),
          '#attributes' => array(
            'class' => array('block-region-select', 'block-region-' . $region),
          ),
        );
        // Allow the weight to be changed for each block.
        $configuration = $block->getConfiguration();
        $row['weight'] = array(
          '#type' => 'weight',
          '#default_value' => isset($configuration['weight']) ? $configuration['weight'] : 0,
          '#title' => t('Weight for @block block', array('@block' => $block->label())),
          '#title_display' => 'invisible',
          '#attributes' => array(
            'class' => array('block-weight', 'block-weight-' . $region),
          ),
        );
        // Add the operation links.
        $operations = array();
        $operations['edit'] = array(
          'title' => $this->t('Edit'),
          'route_name' => 'block_page.page_variant_edit_block',
          'route_parameters' => array(
            'block_page' => $this->blockPage->id(),
            'page_variant_id' => $this->pageVariant->id(),
            'block_id' => $block_id,
          ),
          'attributes' => $attributes,
        );
        $row['operations'] = array(
          '#type' => 'operations',
          '#links' => $operations,
        );
        $form['blocks'][$block_id] = $row;
      }
    }
    // Add a section containing the available blocks to be added to the variant.
    $form['available_blocks'] = array(
      '#type' => 'details',
      '#title' => $this->t('Available blocks'),
      '#attached' => array(
        'library' => array(
          'core/drupal.ajax',
        ),
      ),
    );
    foreach ($this->blockManager->getSortedDefinitions() as $plugin_id => $plugin_definition) {
      // Make a section for each region.
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
      // Add a link for each available block within each region.
      $form['available_blocks'][$category_key]['content']['#links'][$plugin_id] = array(
        'title' => $plugin_definition['admin_label'],
        'route_name' => 'block_page.page_variant_add_block',
        'route_parameters' => array(
          'block_page' => $this->blockPage->id(),
          'page_variant_id' => $this->pageVariant->id(),
          'block_id' => $plugin_id,
        ),
        'attributes' => $attributes,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // If the blocks were rearranged, update their values.
    if (!empty($form_state['values']['blocks'])) {
      foreach ($form_state['values']['blocks'] as $block_id => $block_values) {
        $this->pageVariant->updateBlock($block_id, $block_values);
      }
    }

    // Save the block page.
    $this->blockPage->save();
    drupal_set_message($this->t('The %label page variant has been updated.', array('%label' => $this->pageVariant->label())));
    $form_state['redirect_route'] = $this->blockPage->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  protected function preparePageVariant($page_variant_id) {
    // Load the page variant directly from the block page.
    return $this->blockPage->getPageVariant($page_variant_id);
  }

}
