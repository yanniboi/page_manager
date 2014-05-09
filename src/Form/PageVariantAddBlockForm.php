<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantAddBlockForm.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a block plugin to a page variant.
 */
class PageVariantAddBlockForm extends PageVariantConfigureBlockFormBase {

  /**
   * The block manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs a new PageVariantFormBase.
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
    return 'block_page_page_variant_add_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant = NULL, $plugin_id = NULL) {
    $this->blockPage = $block_page;
    $this->pageVariant = $block_page->getPageVariant($page_variant);
    $this->plugin = $this->blockManager->createInstance($plugin_id);
    $block_id = $this->pageVariant->addBlock($this->plugin->getConfiguration());

    $form = parent::buildForm($form, $form_state, $block_id);

    $form['actions']['submit']['#value'] = $this->t('Add block');
    return $form;
  }

}
