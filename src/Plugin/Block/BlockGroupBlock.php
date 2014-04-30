<?php

/**
 * @file
 * Contains \Drupal\block_group\Plugin\Block\BlockGroupBlock.
 */

namespace Drupal\block_group\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display a specific block group.
 *
 * @Block(
 *   id = "block_group_block",
 *   admin_label = @Translation("Block group"),
 *   category = @Translation("Block group")
 * )
 */
class BlockGroupBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The block group storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $blockGroupStorage;

  /**
   * The block group view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $blockGroupViewBuilder;

  /**
   * Protects against recursion.
   *
   * @var bool
   */
  protected static $recursionProtection = FALSE;

  /**
   * Constructs an AggregatorFeedBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $block_group_storage
   *   The block group storage.
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $block_group_view_builder
   *   The block group view builder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigEntityStorageInterface $block_group_storage, EntityViewBuilderInterface $block_group_view_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockGroupStorage = $block_group_storage;
    $this->blockGroupViewBuilder = $block_group_view_builder;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entity_manager->getStorage('block_group'),
      $entity_manager->getViewBuilder('block_group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'block_group' => NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, &$form_state) {
    $options = array();
    foreach ($this->blockGroupStorage->loadMultiple() as $block_group) {
      $options[$block_group->id()] = $block_group->label();
    }
    $form['block_group'] = array(
      '#type' => 'select',
      '#title' => t('Select the block group that should be displayed'),
      '#default_value' => $this->configuration['block_group'],
      '#options' => $options,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, &$form_state) {
    $this->configuration['block_group'] = $form_state['values']['block_group'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!static::$recursionProtection && ($block_group = $this->blockGroupStorage->load($this->configuration['block_group'])) && $block_group->access('view')) {
      static::$recursionProtection = TRUE;
      return $this->blockGroupViewBuilder->view($block_group);
    }
  }

}
