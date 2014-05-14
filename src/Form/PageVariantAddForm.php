<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\PageVariantAddForm.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\block_page\Plugin\PageVariantManager;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a new page variant.
 */
class PageVariantAddForm extends PageVariantFormBase {

  /**
   * The page variant manager.
   *
   * @var \Drupal\block_page\Plugin\PageVariantManager
   */
  protected $pageVariantManager;

  /**
   * Constructs a new PageVariantAddForm.
   *
   * @param \Drupal\block_page\Plugin\PageVariantManager $page_variant_manager
   *   The page variant manager.
   */
  public function __construct(PageVariantManager $page_variant_manager) {
    $this->pageVariantManager = $page_variant_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.page_variant')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_page_variant_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $page_variant = NULL) {
    $form = parent::buildForm($form, $form_state, $block_page, $page_variant);
    $form['actions']['submit']['#value'] = $this->t('Add page variant');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    $form_state['redirect_route'] = new Url('block_page.page_variant_edit', array(
      'block_page' => $this->blockPage->id(),
      'page_variant' => $this->pageVariant->id(),
    ));
    drupal_set_message($this->t('The %label page variant has been added.', array('%label' => $this->pageVariant->label())));
  }

  /**
   * {@inheritdoc}
   */
  protected function preparePageVariant($page_variant_id) {
    return $this->pageVariantManager->createInstance($page_variant_id);
  }

}
