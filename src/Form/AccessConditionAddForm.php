<?php

/**
 * @file
 * Contains \Drupal\block_page\Form\AccessConditionAddForm.
 */

namespace Drupal\block_page\Form;

use Drupal\Core\Condition\ConditionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a new access condition.
 */
class AccessConditionAddForm extends AccessConditionFormBase {

  /**
   * The condition manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Constructs a new AccessConditionAddForm.
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
    return 'block_page_access_condition_add_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareCondition($access_condition_id) {
    // Create a new access condition instance.
    return $this->conditionManager->createInstance($access_condition_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitText() {
    return $this->t('Add access condition');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // If this access condition is new, add it to the page.
    $this->blockPage->addAccessCondition($this->condition->getConfiguration());

    // Save the block page.
    $this->blockPage->save();
    drupal_set_message($this->t('The %label access condition has been added.', array('%label' => $this->condition->getPluginDefinition()['label'])));
  }

}
