<?php

/**
 * @file
 * Contains \Drupal\block_page\Routing\BlockPageRoutes.
 */

namespace Drupal\block_page\Routing;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides routes for block pages.
 */
class BlockPageRoutes extends RouteSubscriberBase {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Constructs a new BlockPageRoutes.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityStorage = $entity_manager->getStorage('block_page');
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->entityStorage->loadMultiple() as $entity_id => $entity) {
      /** @var $entity \Drupal\block_page\BlockPageInterface */
      $route = new Route(
        $entity->getPath(),
        array(
          '_entity_view' => 'block_page',
          'block_page' => $entity_id,
          '_title' => $entity->label(),
        ),
        array(
          '_entity_access' => 'block_page.view',
        ),
        array(
          'parameters' => array(
            'block_page' => array(
              'type' => 'entity:block_page',
            ),
          ),
        )
      );
      $collection->add("block_page.page_view_$entity_id", $route);
    }
  }

}
