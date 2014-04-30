<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\BlockGroupAddBlockForm.
 */

namespace Drupal\block_group\Form;

use Drupal\block_group\BlockGroupInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a block plugin to a block group.
 */
class BlockGroupAddBlockForm extends BlockGroupConfigureBlockFormBase {

  /**
   * The block manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs a new BlockGroupFormBase.
   */
  public function __construct(PluginManagerInterface $block_manager) {
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
    return 'block_group_add_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockGroupInterface $block_group = NULL, $plugin_id = NULL) {
    $this->plugin = $this->blockManager->createInstance($plugin_id);
    $block_id = $block_group->addBlockToGroup($this->plugin->getConfiguration());

    $form = parent::buildForm($form, $form_state, $block_group, $block_id);

    $form['actions']['submit']['#value'] = $this->t('Add block');
    return $form;
  }

}
