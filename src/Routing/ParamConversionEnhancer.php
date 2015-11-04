<?php

/**
 * @file
 * Contains \Drupal\page_manager\Routing\ParamConversionEnhancer.
 */

namespace Drupal\page_manager\Routing;

use Drupal\Core\Routing\Enhancer\ParamConversionEnhancer as CoreParamConversionEnhancer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a ParamConversionEnhancer that only runs once per request object.
 *
 * @todo Remove once https://www.drupal.org/node/2605250 is committed.
 */
class ParamConversionEnhancer extends CoreParamConversionEnhancer {

  /**
   * {@inheritdoc}
   */
  public function enhance(array $defaults, Request $request) {
    // Only run param conversion if it hasn't yet been run for this request.
    if (!isset($defaults['_raw_variables'])) {
      $defaults = parent::enhance($defaults, $request);
    }
    return $defaults;
  }

}
