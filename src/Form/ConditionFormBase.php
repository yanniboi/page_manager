<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\ConditionFormBase.
 */

namespace Drupal\block_page\Form;

use Drupal\block_page\BlockPageInterface;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Form\FormBase;

/**
 * @todo.
 */
abstract class ConditionFormBase extends FormBase {

  /**
   * The block page this condition belongs to.
   *
   * @var \Drupal\block_page\BlockPageInterface
   */
  protected $blockPage;

  /**
   * The condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $condition;

  /**
   * Prepares the condition used by this form.
   *
   * @param string $condition_id
   *   Either a condition ID, or the plugin ID used to create a new
   *   condition.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The condition object.
   */
  abstract protected function prepareCondition($condition_id);

  /**
   * Returns the text to use for the submit button.
   *
   * @return string
   *   The submit button text.
   */
  abstract protected function submitText();

  /**
   * @return \Drupal\block_page\ContextHandler
   */
  protected function contextHandler() {
    return \Drupal::service('context.handler');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, BlockPageInterface $block_page = NULL, $condition_id = NULL) {
    $this->blockPage = $block_page;
    $this->condition = $this->prepareCondition($condition_id);

    // Allow the condition to add to the form.
    $form['condition'] = $this->condition->buildConfigurationForm(array(), $form_state);
    $form['condition']['#tree'] = TRUE;

    if ($this->condition instanceof ContextAwarePluginInterface) {
      $form['context_assignments'] = $this->contextHandler()->addContextAssignmentElement($this->condition->getContextDefinitions(), $this->blockPage->getContexts());
    }

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
    // Allow the condition to validate the form.
    $condition_values = array(
      'values' => &$form_state['values']['condition'],
    );
    $this->condition->validateConfigurationForm($form, $condition_values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Allow the condition to submit the form.
    $condition_values = array(
      'values' => &$form_state['values']['condition'],
    );
    $this->condition->submitConfigurationForm($form, $condition_values);

    if (!empty($form_state['values']['context_assignments'])) {
      // @todo Consider creating a ContextAwareConditionPluginBase to handle this.
      $configuration = $this->condition->getConfiguration();
      $configuration['context_assignments'] = $form_state['values']['context_assignments'];
      $this->condition->setConfiguration($configuration);
    }
  }

}
