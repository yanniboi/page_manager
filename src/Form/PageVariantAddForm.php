<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantAddForm.
 */

namespace Drupal\page_manager\Form;

use Drupal\page_manager\Plugin\PageVariantManager;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a new page variant.
 */
class PageVariantAddForm extends PageVariantFormBase {

  /**
   * The page variant manager.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantManager
   */
  protected $pageVariantManager;

  /**
   * Constructs a new PageVariantAddForm.
   *
   * @param \Drupal\page_manager\Plugin\PageVariantManager $page_variant_manager
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
    return 'page_manager_page_variant_add_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Add page variant');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // If this page variant is new, add it to the page.
    $page_variant_id = $this->page->addVariant($this->pageVariant->getConfiguration());

    // Save the page entity.
    $this->page->save();
    drupal_set_message($this->t('The %label page variant has been added.', array('%label' => $this->pageVariant->label())));
    $form_state['redirect_route'] = new Url('page_manager.page_variant_edit', array(
      'page' => $this->page->id(),
      'page_variant_id' => $page_variant_id,
    ));
  }

  /**
   * {@inheritdoc}
   */
  protected function preparePageVariant($page_variant_id) {
    // Create a new page variant instance.
    return $this->pageVariantManager->createInstance($page_variant_id);
  }

}
