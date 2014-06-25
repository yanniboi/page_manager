<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\PageAwareVariantInterface.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Core\Display\VariantInterface;
use Drupal\page_manager\PageExecutable;

/**
 * Provides an interface for variant plugins that are Page-aware.
 */
interface PageAwareVariantInterface extends VariantInterface {

  /**
   * Initializes the display variant.
   *
   * Only used during runtime.
   *
   * @param \Drupal\page_manager\PageExecutable $executable
   *  The page executable.
   *
   * @return $this
   */
  public function setExecutable(PageExecutable $executable);

}
