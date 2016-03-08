<?php

/**
 * @file
 * Contains \Drupal\page_manager_ui\Routing\RouteSubscriber.
 */

namespace Drupal\page_manager_ui\Routing;

use Drupal\ctools\Routing\DisplayRouteSubscriberBase;

class RouteSubscriber extends \Drupal\ctools\Routing\DisplayRouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntityTypeId() {
    return 'page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getBasePath() {
    return '/admin/structure/page_manager';
  }

  /**
   * {@inheritdoc}
   */
  protected function getCollectionTitle() {
    return 'Pages';
  }

  /**
   * {@inheritdoc}
   */
  protected function getAddTitle() {
    return 'Add page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeleteTitle() {
    return 'Delete page';
  }

}
