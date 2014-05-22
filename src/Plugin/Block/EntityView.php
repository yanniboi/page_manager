<?php

/**
 * @file
 * Contains \Drupal\Core\Entity\Plugin\Block\EntityView.
 */

namespace Drupal\page_manager\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to view a specific entity.
 *
 * @todo Provide the ability to select a view mode.
 *
 * @Block(
 *   id = "entity_view",
 *   derivative = "Drupal\page_manager\Plugin\Deriver\EntityViewDeriver",
 * )
 */
class EntityView extends BlockBase implements ContextAwarePluginInterface, ContainerFactoryPluginInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new EntityView.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var $entity \Drupal\Core\Entity\EntityInterface */
    $entity = $this->getContextValue('entity');
    $view_builder = $this->entityManager->getViewBuilder($entity->getEntityTypeId());
    return $view_builder->view($entity);
  }

}
