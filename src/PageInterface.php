<?php

/**
 * @file
 * Contains \Drupal\page_manager\PageInterface.
 */

namespace Drupal\page_manager;

use Drupal\ctools\Entity\DisplayInterface;

/**
 * Provides an interface for page entities.
 */
interface PageInterface extends DisplayInterface {

  /**
   * Returns the path for the page entity.
   *
   * @return string
   *   The path for the page entity.
   */
  public function getPath();

  /**
   * Indicates if this page is an admin page or not.
   *
   * @return bool
   *   TRUE if this is an admin page, FALSE otherwise.
   */
  public function usesAdminTheme();

  /**
   * Gets the names of all parameters for this page.
   *
   * @return string[]
   */
  public function getParameterNames();

}
