<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\PageVariantFormBase.
 */

namespace Drupal\page_manager\Form;

use Drupal\page_manager\PageInterface;
use Drupal\Core\Form\FormBase;

/**
 * Provides a base form for editing and adding a page variant.
 */
abstract class PageVariantFormBase extends FormBase {

  /**
   * The page entity this page variant belongs to.
   *
   * @var \Drupal\page_manager\PageInterface
   */
  protected $page;

  /**
   * The page variant used by this form.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * Prepares the page variant used by this form.
   *
   * @param string $page_variant_id
   *   Either a page variant ID, or the plugin ID used to create a new variant.
   *
   * @return \Drupal\page_manager\Plugin\PageVariantInterface
   *   The page variant object.
   */
  abstract protected function preparePageVariant($page_variant_id);

  /**
   * Returns the text to use for the submit button.
   *
   * @return string
   *   The submit button text.
   */
  abstract protected function submitText();

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, PageInterface $page = NULL, $page_variant_id = NULL) {
    $this->page = $page;
    $this->pageVariant = $this->preparePageVariant($page_variant_id);

    // Allow the page variant to add to the form.
    $form['page_variant'] = $this->pageVariant->buildConfigurationForm(array(), $form_state);
    $form['page_variant']['#tree'] = TRUE;

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->submitText(),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    // Allow the page variant to validate the form.
    $page_variant_values = array(
      'values' => &$form_state['values']['page_variant'],
    );
    $this->pageVariant->validateConfigurationForm($form, $page_variant_values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Allow the page variant to submit the form.
    $page_variant_values = array(
      'values' => &$form_state['values']['page_variant'],
    );
    $this->pageVariant->submitConfigurationForm($form, $page_variant_values);
  }

}
