<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\PageViewBuilder.
 */

namespace Drupal\page_manager\Entity;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a view builder for page entities.
 */
class PageViewBuilder extends EntityViewBuilder {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new PageViewBuilder.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, RouteMatchInterface $route_match) {
    parent::__construct($entity_type, $entity_manager, $language_manager);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = [];
    /** @var \Drupal\page_manager\PageInterface $entity */
    if ($display_variant = $entity->getExecutable()->getRuntimeVariant($this->routeMatch->getParameter('variant_id'))) {
      if ($display_variant instanceof RefinableCacheableDependencyInterface) {
        $display_variant->addCacheableDependency($entity);
      }
      $build = $display_variant->build();
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = [], $view_mode = 'full', $langcode = NULL) {
    $build = [];
    foreach ($entities as $key => $entity) {
      $build[$key] = $this->view($entity, $view_mode, $langcode);
    }
    return $build;
  }

}
