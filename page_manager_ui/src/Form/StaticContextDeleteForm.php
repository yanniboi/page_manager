<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\StaticContextDeleteForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\user\SharedTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting an access condition.
 */
class StaticContextDeleteForm extends ConfirmFormBase {

  /**
   * @var \Drupal\user\SharedTempStoreFactory
   */
  protected $tempstore;

  /**
   * @var string
   */
  protected $tempstore_id;

  /**
   * @var string
   */
  protected $machine_name;

  /**
   * The machine-name of the variant.
   *
   * @var string
   */
  protected $variantMachineName;

  /**
   * The static context's machine name.
   *
   * @var array
   */
  protected $data_type;

  public static function create(ContainerInterface $container) {
    return new static($container->get('user.shared_tempstore'));
  }

  public function __construct(SharedTempStoreFactory $tempstore) {
    $this->tempstore = $tempstore;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_static_context_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $cached_values = $this->getTempstore();
    /** @var $page \Drupal\page_manager\PageInterface */
    $page_variant = $this->getPageVariant($cached_values);
    return $this->t('Are you sure you want to delete the static context %label?', ['%label' => $page_variant->getStaticContext($this->data_type)['label']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    $cached_values = $this->getTempstore();
    /** @var $page \Drupal\page_manager\PageInterface */
    $page = $cached_values['page'];

    if ($page->isNew()) {
      return new Url('entity.page.add_step_form', [
        'machine_name' => $this->machine_name,
        'step' => 'selection',
      ]);
    }
    else {
      $page_variant = $this->getPageVariant($cached_values);
      return new Url('entity.page.edit_form', [
        'machine_name' => $this->machine_name,
        'step' => 'page_variant__' . $page_variant->id() . '__contexts',
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $tempstore_id = NULL, $machine_name = NULL, $variant_machine_name = NULL, $data_type = NULL) {
    $this->tempstore_id = $tempstore_id;
    $this->machine_name = $machine_name;
    $this->variantMachineName = $variant_machine_name;
    $this->data_type = $data_type;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cached_values = $this->getTempstore();
    /** @var $page \Drupal\page_manager\PageInterface */
    $page_variant = $this->getPageVariant($cached_values);
    drupal_set_message($this->t('The static context %label has been removed.', ['%label' => $page_variant->getStaticContext($this->data_type)['label']]));
    $page_variant->removeStaticContext($this->data_type);
    $this->setTempstore($cached_values);
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  protected function getTempstore() {
    return $this->tempstore->get($this->tempstore_id)->get($this->machine_name);
  }

  protected function setTempstore($cached_values) {
    $this->tempstore->get($this->tempstore_id)->set($this->machine_name, $cached_values);
  }

  /**
   * Get the page variant.
   *
   * @param array $cached_values
   *   The cached values from the wizard.
   *
   * @return \Drupal\page_manager\PageVariantInterface
   */
  protected function getPageVariant($cached_values) {
    if (isset($cached_values['page_variant'])) {
      return $cached_values['page_variant'];
    }

    /** @var $page \Drupal\page_manager\PageInterface */
    $page = $cached_values['page'];
    return $page->getVariant($this->variantMachineName);
  }

}
