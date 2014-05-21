<?php

/**
 * @file
 * Contains \Drupal\page_manager\Annotation\PageVariant.
 */

namespace Drupal\page_manager\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a page variant annotation object.
 *
 * @Annotation
 */
class PageVariant extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The administrative label.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $admin_label = '';

}
