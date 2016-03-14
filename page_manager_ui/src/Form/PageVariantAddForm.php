<?php

/**
 * @file
 * Contains Drupal\page_manager_ui\Form\PageVariantAddForm.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Display\VariantManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\page_manager\PageInterface;
use Drupal\user\SharedTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a variant.
 */
class PageVariantAddForm extends FormBase {

  /**
   * The variant manager.
   *
   * @var \Drupal\Core\Display\VariantManager
   */
  protected $variantManager;

  /**
   * Tempstore factory.
   *
   * @var \Drupal\user\SharedTempStoreFactory
   */
  protected $tempstore;

  /**
   * Constructs a new DisplayVariantAddForm.
   *
   * @param \Drupal\Core\Display\VariantManager $variant_manager
   *   The variant manager.
   */
  public function __construct(VariantManager $variant_manager, SharedTempStoreFactory $tempstore) {
    $this->variantManager = $variant_manager;
    $this->tempstore = $tempstore;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.display_variant'),
      $container->get('user.shared_tempstore')
    );
  }

  /**
   * Get the tempstore id.
   *
   * @return string
   */
  protected function getTempstoreId() {
    return 'page_manager.page';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_add_variant_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $machine_name = '') {
    $cached_values = $this->tempstore->get($this->getTempstoreId())->get($machine_name);
    $form_state->setTemporaryValue('wizard', $cached_values);
    /** @var $page \Drupal\page_manager\Entity\Page */
    $page = $cached_values['page'];

    $variant_plugin_options = [];
    foreach ($this->variantManager->getDefinitions() as $plugin_id => $definition) {
      $variant_plugin_options[$plugin_id] = $definition['admin_label'];
    }
    $form['variant_plugin_id'] = [
      '#title' => $this->t('Type'),
      '#type' => 'select',
      '#options' => $variant_plugin_options,
      '#default_value' => !empty($cached_values['variant_plugin_id']) ? $cached_values['variant_plugin_id'] : '',
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#required' => TRUE,
      '#size' => 32,
      '#maxlength' => 255,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#maxlength' => 128,
      '#machine_name' => [
        'source' => array('label'),
        'exists' => function ($id) use ($page) {
          return $this->variantExists($page, $id);
        },
      ],
      '#description' => $this->t('A unique machine-readable name for this variant.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create variant'),
    ];

    return $form;
  }

  /**
   * Check if a variant id is taken.
   *
   * @param \Drupal\page_manager\PageInterface $page
   *   The page entity.
   * @param string $variant_id
   *   The page variant id to check.
   *
   * @return bool
   *   TRUE if the ID is available; FALSE otherwise.
   */
  protected function variantExists(PageInterface $page, $variant_id) {
    return isset($page->getVariants()[$variant_id]) || PageVariant::load($variant_id);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cached_values = $form_state->getTemporaryValue('wizard');
    /** @var \Drupal\page_manager\Entity\Page $page */
    $page = $cached_values['page'];

    $cached_values['page_variant'] = \Drupal::entityManager()
      ->getStorage('page_variant')
      ->create([
        'variant' => $form_state->getValue('variant_plugin_id'),
        'page' => $page->id(),
        'id' => $form_state->getValue('id'),
        'label' => $form_state->getValue('label'),
      ]);
    $page->addVariant($cached_values['page_variant']);

    $form_state->setRedirect('entity.page.edit_form', [
      'machine_name' => $page->id(),
      'step' => 'page_variant__' . $cached_values['page_variant']->id() . '__overview',
    ]);

    $this->tempstore->get($this->getTempstoreId())->set($page->id(), $cached_values);
  }

}
