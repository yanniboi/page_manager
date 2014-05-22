<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\PageVariant\DefaultPageVariant.
 */

namespace Drupal\page_manager\Plugin\PageVariant;

use Drupal\Core\Session\AccountInterface;
use Drupal\page_manager\ContextHandler;
use Drupal\page_manager\Plugin\ConditionAccessResolverTrait;
use Drupal\page_manager\Plugin\PageVariantBase;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a default page variant.
 */
class DefaultPageVariant extends PageVariantBase implements ContainerFactoryPluginInterface {

  /**
   * The context handler.
   *
   * @var \Drupal\page_manager\ContextHandler
   */
  protected $contextHandler;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Constructs a new DefaultPageVariant.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\page_manager\ContextHandler $context_handler
   *   The context handler.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContextHandler $context_handler, AccountInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->contextHandler = $context_handler;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('context.handler'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionNames() {
    // @todo Reference an external object of some kind, like a Layout.
    return array(
      'top' => 'Top',
      'bottom' => 'Bottom',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = array();
    $contexts = $this->getContexts();
    foreach ($this->getRegionAssignments() as $region => $blocks) {
      if (!$blocks) {
        continue;
      }

      $region_name = drupal_html_class("block-region-$region");
      $build[$region]['#prefix'] = '<div class="' . $region_name . '">';
      $build[$region]['#suffix'] = '</div>';

      /** @var $blocks \Drupal\block\BlockPluginInterface[] */
      foreach ($blocks as $block_id => $block) {
        if ($block instanceof ContextAwarePluginInterface) {
          $this->contextHandler->preparePluginContext($block, $contexts);
        }
        if ($block->access($this->account)) {
          $row = $block->build();
          $block_name = drupal_html_class("block-$block_id");
          $row['#prefix'] = '<div class="' . $block_name . '">';
          $row['#suffix'] = '</div>';

          $build[$region][$block_id] = $row;
        }
      }
    }
    return $build;
  }

}
