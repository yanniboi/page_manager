<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\SelectionConditionAddForm.
 */

namespace Drupal\block_page\Form;

use Drupal\Core\Condition\ConditionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a new selection condition.
 */
class SelectionConditionAddForm extends SelectionConditionFormBase {

  /**
   * The condition manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Constructs a new SelectionConditionAddForm.
   *
   * @param \Drupal\Core\Condition\ConditionManager $condition_manager
   *   The condition manager.
   */
  public function __construct(ConditionManager $condition_manager) {
    $this->conditionManager = $condition_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_page_selection_condition_add_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareCondition($selection_condition_id) {
    // Create a new selection condition instance.
    return $this->conditionManager->createInstance($selection_condition_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Add selection condition');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // If this selection condition is new, add it to the page.
    $this->pageVariant->addSelectionCondition($this->condition->getConfiguration());

    // Save the block page.
    $this->blockPage->save();
    drupal_set_message($this->t('The %label selection condition has been added.', array('%label' => $this->condition->getPluginDefinition()['label'])));
  }

}
