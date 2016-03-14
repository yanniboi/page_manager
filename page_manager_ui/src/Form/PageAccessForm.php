<?php
/**
 * @file
 * Contains \Drupal\page_manager_ui\Form\PageAccessForm.
 */

namespace Drupal\page_manager_ui\Form;


use Drupal\ctools\Form\ManageConditions;

class PageAccessForm extends ManageConditions {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_access_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getConditionClass() {
    return 'Drupal\page_manager_ui\Form\AccessConfigure';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTempstoreId() {
    return 'page_manager.page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getOperationsRouteInfo($cached_values, $machine_name, $row) {
    return ['entity.page.condition', ['machine_name' => $machine_name, 'condition' => $row]];
  }

  /**
   * {@inheritdoc}
   */
  protected function getConditions($cached_values) {
    /** @var $page \Drupal\page_manager\Entity\Page */
    $page = $cached_values['page'];
    return $page->get('access_conditions');
  }

  /**
   * {@inheritdoc}
   */
  protected function getContexts($cached_values) {
    /** @var $page \Drupal\page_manager\Entity\Page */
    $page = $cached_values['page'];
    return $page->getContexts();
  }

  /**
   * The route to which condition 'add' actions should submit.
   *
   * @return string
   */
  protected function getAddRoute($cached_values) {
    return 'entity.page.condition.add';
  }

}
