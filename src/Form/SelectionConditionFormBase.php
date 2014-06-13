<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\SelectionConditionFormBase.
 */

namespace Drupal\page_manager\Form;

use Drupal\page_manager\PageInterface;
use Drupal\Core\Url;

/**
 * Provides a base form for editing and adding a selection condition.
 */
abstract class SelectionConditionFormBase extends ConditionFormBase {

  /**
   * The page variant.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL, $condition_id = NULL) {
    $this->pageVariant = $page->getVariant($page_variant_id);
    return parent::buildForm($form, $form_state, $page, $condition_id);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $configuration = $this->condition->getConfiguration();
    // If this selection condition is new, add it to the page.
    if (!isset($configuration['uuid'])) {
      $this->pageVariant->addSelectionCondition($configuration);
    }

    // Save the page entity.
    $this->page->save();

    $form_state['redirect_route'] = new Url('page_manager.page_variant_edit', array(
      'page' => $this->page->id(),
      'page_variant_id' => $this->pageVariant->id(),
    ));
  }

}
