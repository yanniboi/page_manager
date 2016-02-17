<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\PageVariantConfigMapper.
 */

namespace Drupal\page_manager\Entity;

use Drupal\config_translation\ConfigEntityMapper;

/**
 * Configuration mapper for page variants.
 *
 * @todo Remove once https://www.drupal.org/node/2670712 is in.
 */
class PageVariantConfigMapper extends ConfigEntityMapper {

  /**
   * {@inheritdoc}
   */
  public function getBaseRouteParameters() {
    $parameters = parent::getBaseRouteParameters();
    $parameters['page'] = $this->entity->get('page');
    return $parameters;
  }

}
