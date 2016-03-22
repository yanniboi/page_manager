<?php
/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\StaticContextConfigure.
 */

namespace Drupal\page_manager_ui\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\user\SharedTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StaticContextConfigure extends FormBase {

  /**
   * @var \Drupal\Core\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

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

  public static function create(ContainerInterface $container) {
    return new static($container->get('typed_data_manager'), $container->get('user.shared_tempstore'));
  }

  public function __construct(TypedDataManagerInterface $typed_data_manager, SharedTempStoreFactory $tempstore) {
    $this->typedDataManager = $typed_data_manager;
    $this->tempstore = $tempstore;
  }

  protected function getTempstore() {
    return $this->tempstore->get($this->tempstore_id)->get($this->machine_name);
  }

  protected function setTempstore($cached_values) {
    $this->tempstore->get($this->tempstore_id)->set($this->machine_name, $cached_values);
  }

  public function getFormId() {
    return 'page_manager_configure_static_context';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $op = 'edit', $data_type = NULL, $tempstore_id = NULL, $machine_name = NULL, $variant_machine_name = NULL) {
    $this->tempstore_id = $tempstore_id;
    $this->machine_name = $machine_name;
    $this->variantMachineName = $variant_machine_name;
    $cached_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page_variant = $this->getPageVariant($cached_values);
    if ($op == 'edit') {
      $context = $page_variant->getStaticContext($data_type);
      $form['type'] = [
        '#type' => 'value',
        '#value' => $context['type'],
      ];
    }
    else {
      $form['type'] = [
        '#type' => 'value',
        '#value' => $data_type,
      ];
    }
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Context label'),
      '#description' => $this->t('Provide a label for this context object for use in the user interface.'),
      '#default_value' => isset($context) ? $context['label'] : '',
      '#required' => TRUE,
      '#size' => 32,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#maxlength' => 128,
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
      '#description' => $this->t('A unique machine-readable name for this context.'),
      '#default_value' => ($op == 'edit') ? $data_type : '',
      '#disabled' => ($op == 'edit'),
    ];
    $type = ($op == 'edit' && isset($context)) ? $context['type'] : $data_type;
    if (substr($type, 0, 7) == 'entity:') {
      if (isset($context) && ($context['value'] !== NULL)) {
        $value_entity = \Drupal::entityManager()->loadEntityByUuid(substr($type, 7), $context['value']);
      }
      $form['value'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Choose an entity to reference'),
        '#target_type' => substr($type, 7),
        '#maxlength' => 1024,
        '#default_value' => isset($value_entity) ? $value_entity : NULL,
      ];
    }
    else {
      $form['value'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter a value for the context'),
        '#default_value' => isset($context) ? $context['value'] : NULL,
      ];
    }
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => [$this, 'ajaxSave'],
      ]
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cached_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page_variant = $this->getPageVariant($cached_values);
    $value = $form_state->getValue('value');
    $type = $form_state->getValue('type');
    if ((substr($type, 0, 7) == 'entity:') && ($value !== NULL))  {
      $entity = \Drupal::entityTypeManager()->getStorage(substr($type, 7))->load($value);
      $value = $entity->uuid();
    }
    $config = [
      'label' => $form_state->getValue('label'),
      'type' => $form_state->getValue('type'),
      'value' => $value,
    ];
    $page_variant->setStaticContext($form_state->getValue('id'), $config);
    $this->setTempstore($cached_values);
    list($route_name, $route_parameters) = $this->getParentRouteInfo($cached_values);
    $form_state->setRedirect($route_name, $route_parameters);
  }

  public function ajaxSave(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $cached_values = $this->tempstore->get($this->tempstore_id)->get($this->machine_name);
    list($route_name, $route_parameters) = $this->getParentRouteInfo($cached_values);
    $response->addCommand(new RedirectCommand($this->url($route_name, $route_parameters)));
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  public function exists($key, $element, FormStateInterface $form_state) {
    $cached_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page = $cached_values['page'];
    $contexts = $page->getContexts();
    return !empty($contexts[$key]);
  }

  protected function getParentRouteInfo($cached_values) {
    /** @var $page \Drupal\page_manager\PageInterface */
    $page = $cached_values['page'];

    if ($page->isNew()) {
      return ['entity.page.add_step_form', [
        'machine_name' => $this->machine_name,
        'step' => 'contexts',
      ]];
    }
    else {
      $page_variant = $this->getPageVariant($cached_values);
      return ['entity.page.edit_form', [
        'machine_name' => $this->machine_name,
        'step' => 'page_variant__' . $page_variant->id() . '__contexts',
      ]];
    }
  }

  /**
   * Returns the page variant.
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
