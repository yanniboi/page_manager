<?php

/**
 * @file
 * Contains \Drupal\page_manager\PageManagerServiceProvider.
 */

namespace Drupal\page_manager;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\page_manager\Routing\ParamConversionEnhancer;

/**
 * Allows page manager to alter the container during build.
 */
class PageManagerServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // @todo Remove when https://www.drupal.org/node/2605250 is committed.
    $container->getDefinition('route_enhancer.param_conversion')->setClass(ParamConversionEnhancer::class);
  }

}
