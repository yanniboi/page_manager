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

  public function buildForm(array $form, FormStateInterface $form_state, $op = 'edit', $data_type = NULL, $tempstore_id = NULL, $machine_name = NULL) {
    $this->tempstore_id = $tempstore_id;
    $this->machine_name = $machine_name;
    $cached_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page = $cached_values['page'];
    if ($op == 'edit') {
      $context = $page->getStaticContext($data_type);
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
    if (isset($context)) {
      $entities = \Drupal::entityTypeManager()->getStorage(substr($type, 7))->loadByProperties(['uuid' => $context['value']]);
      $value = array_shift($entities);
    }
    else {
      $value = NULL;
    }
    if (substr($type, 0, 7) == 'entity:') {
      $form['value'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Choose an entity to reference'),
        '#target_type' => substr($type, 7),
        '#maxlength' => 1024,
        '#default_value' => $value,
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
    $cache_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page = $cache_values['page'];
    $value = $form_state->getValue('value');
    $type = $form_state->getValue('type');
    if (substr($type, 0, 7) == 'entity:') {
      $entity = \Drupal::entityTypeManager()->getStorage(substr($type, 7))->load($value);
      $value = $entity->uuid();
    }
    $config = [
      'label' => $form_state->getValue('label'),
      'type' => $form_state->getValue('type'),
      'value' => $value,
    ];
    $page->setStaticContext($form_state->getValue('id'), $config);
    $this->setTempstore($cache_values);
    list($route_name, $route_parameters) = $this->getParentRouteInfo($cache_values);
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
    $cache_values = $this->getTempstore();
    /** @var \Drupal\page_manager\PageInterface $page */
    $page = $cache_values['page'];
    $contexts = $page->getContexts();
    return !empty($contexts[$key]);
  }

  protected function getParentRouteInfo($cached_values) {
    /** @var $page \Drupal\page_manager\PageInterface */
    $page = $cached_values['page'];

    $route_name = $page->isNew() ? 'entity.page.add_step_form' : 'entity.page.edit_form';
    return [$route_name, [
      'machine_name' => $this->machine_name,
      'step' => 'contexts',
    ]];
  }

}
