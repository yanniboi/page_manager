<?php

/**
 * @file
 * Contains \Drupal\page_manager\PageExecutableFactory.
 */

namespace Drupal\page_manager;

use Drupal\page_manager\PageExecutable;
use Drupal\page_manager\PageInterface;

/**
 * Provides a factory for page executables.
 */
class PageExecutableFactory implements PageExecutableFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function get(PageInterface $page) {
    return new PageExecutable($page);
  }

}
