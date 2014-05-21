<?php

/**
 * @file
 * Contains \Drupal\block_page\Controller\BlockPageController.
 */

namespace Drupal\block_page\Controller;

use Drupal\block\BlockManagerInterface;
use Drupal\block_page\BlockPageInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route controllers for block page.
 */
class BlockPageController extends ControllerBase {

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
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   *
   * @return string
   *   The title for the block page edit form.
   */
  public function editBlockPageTitle(BlockPageInterface $block_page) {
    return $this->t('Edit %label block page', array('%label' => $block_page->label()));
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return string
   *   The title for the page variant edit form.
   */
  public function editPageVariantTitle(BlockPageInterface $block_page, $page_variant_id) {
    $page_variant = $block_page->getPageVariant($page_variant_id);
    return $this->t('Edit %label page variant', array('%label' => $page_variant->label()));
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $access_condition_id
   *   The access condition ID.
   *
   * @return string
   *   The title for the access condition edit form.
   */
  public function editAccessConditionTitle(BlockPageInterface $block_page, $access_condition_id) {
    $access_condition = $block_page->getAccessCondition($access_condition_id);
    return $this->t('Edit %label access condition', array('%label' => $access_condition->getPluginDefinition()['label']));
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $page_variant_id
   *   The page variant ID.
   * @param string $selection_condition_id
   *   The selection condition ID.
   *
   * @return string
   *   The title for the selection condition edit form.
   */
  public function editSelectionConditionTitle(BlockPageInterface $block_page, $page_variant_id, $selection_condition_id) {
    $page_variant = $block_page->getPageVariant($page_variant_id);
    $selection_condition = $page_variant->getSelectionCondition($selection_condition_id);
    return $this->t('Edit %label selection condition', array('%label' => $selection_condition->getPluginDefinition()['label']));
  }

  /**
   * Presents a list of page variants to add to the block page.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   *
   * @return array
   *   The page variant selection page.
   */
  public function selectPageVariant(BlockPageInterface $block_page) {
    $build = array(
      '#theme' => 'links',
      '#links' => array(),
    );
    $page_variant_manager = \Drupal::service('plugin.manager.page_variant');
    foreach ($page_variant_manager->getDefinitions() as $page_variant_id => $page_variant) {
      $build['#links'][$page_variant_id] = array(
        'title' => $page_variant['admin_label'],
        'route_name' => 'block_page.page_variant_add',
        'route_parameters' => array(
          'block_page' => $block_page->id(),
          'page_variant_id' => $page_variant_id,
        ),
        'attributes' => array(
          'class' => array('use-ajax'),
          'data-accepts' => 'application/vnd.drupal-modal',
          'data-dialog-options' => Json::encode(array(
            'width' => 'auto',
          )),
        ),
      );
    }
    return $build;
  }

  /**
   * Presents a list of access conditions to add to the block page.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   *
   * @return array
   *   The access condition selection page.
   */
  public function selectAccessCondition(BlockPageInterface $block_page) {
    $build = array(
      '#theme' => 'links',
      '#links' => array(),
    );
    $available_plugins = $this->getAvailableConditionPlugins($block_page->getContexts());
    foreach ($available_plugins as $access_id => $access_condition) {
      $build['#links'][$access_id] = array(
        'title' => $access_condition['label'],
        'route_name' => 'block_page.access_condition_add',
        'route_parameters' => array(
          'block_page' => $block_page->id(),
          'access_condition_id' => $access_id,
        ),
        'attributes' => array(
          'class' => array('use-ajax'),
          'data-accepts' => 'application/vnd.drupal-modal',
          'data-dialog-options' => Json::encode(array(
            'width' => 'auto',
          )),
        ),
      );
    }
    return $build;
  }

  /**
   * Presents a list of selection conditions to add to the block page.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return array
   *   The selection condition selection page.
   */
  public function selectSelectionCondition(BlockPageInterface $block_page, $page_variant_id) {
    $build = array(
      '#theme' => 'links',
      '#links' => array(),
    );
    $available_plugins = $this->getAvailableConditionPlugins($block_page->getContexts());
    foreach ($available_plugins as $selection_id => $selection_condition) {
      $build['#links'][$selection_id] = array(
        'title' => $selection_condition['label'],
        'route_name' => 'block_page.selection_condition_add',
        'route_parameters' => array(
          'block_page' => $block_page->id(),
          'page_variant_id' => $page_variant_id,
          'selection_condition_id' => $selection_id,
        ),
        'attributes' => array(
          'class' => array('use-ajax'),
          'data-accepts' => 'application/vnd.drupal-modal',
          'data-dialog-options' => Json::encode(array(
            'width' => 'auto',
          )),
        ),
      );
    }
    return $build;
  }

  /**
   * Presents a list of blocks to add to the page variant.
   *
   * @param \Drupal\block_page\BlockPageInterface $block_page
   *   The block page.
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return array
   *   The block selection page.
   */
  public function selectBlock(BlockPageInterface $block_page, $page_variant_id) {
    // Add a section containing the available blocks to be added to the variant.
    $build = array(
      '#type' => 'container',
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
      if (!isset($build[$category_key])) {
        $build[$category_key] = array(
          '#type' => 'fieldgroup',
          '#title' => $category,
          'content' => array(
            '#theme' => 'links',
          ),
        );
      }
      // Add a link for each available block within each region.
      $build[$category_key]['content']['#links'][$plugin_id] = array(
        'title' => $plugin_definition['admin_label'],
        'route_name' => 'block_page.page_variant_add_block',
        'route_parameters' => array(
          'block_page' => $block_page->id(),
          'page_variant_id' => $page_variant_id,
          'block_id' => $plugin_id,
        ),
        'attributes' => array(
          'class' => array('use-ajax'),
          'data-accepts' => 'application/vnd.drupal-modal',
          'data-dialog-options' => Json::encode(array(
            'width' => 'auto',
          )),
        ),
      );
    }
    return $build;
  }

  /**
   * Returns condition plugins for a given set of contexts.
   *
   * @param \Drupal\Component\Plugin\Context\ContextInterface[] $contexts
   *   A set of contexts.
   *
   * @return \Drupal\Core\Condition\ConditionInterface[]
   *   A set of condition plugins that are satisfied by those contexts.
   */
  protected function getAvailableConditionPlugins(array $contexts) {
    $condition_manager = \Drupal::service('plugin.manager.condition');
    $context_handler = \Drupal::service('context.handler');
    return $context_handler->getAvailablePlugins($contexts, $condition_manager);
  }

}
