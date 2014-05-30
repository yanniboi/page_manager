<?php

/**
 * @file
 * Contains \Drupal\page_manager\Event\PageManagerContextEvent.
 */

namespace Drupal\page_manager\Event;

use Drupal\page_manager\PageExecutable;
use Drupal\page_manager\Plugin\PageVariantInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps a page entity for event subscribers.
 *
 * @see \Drupal\page_manager\Event\PageManagerEvents::PAGE_CONTEXT
 */
class PageManagerContextEvent extends Event {

  /**
   * The page the context is gathered for.
   *
   * @var \Drupal\page_manager\PageInterface
   */
  protected $page;

  /**
   * The page variant.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantInterface
   */
  protected $pageVariant;

  /**
   * Creates a new PageManagerContextEvent.
   *
   * @param \Drupal\page_manager\PageExecutable $page
   *   The page executable.
   * @param \Drupal\page_manager\Plugin\PageVariantInterface $page_variant
   *   (optional) The page variant.
   */
  public function __construct(PageExecutable $page, PageVariantInterface $page_variant = NULL) {
    $this->page = $page;
    $this->pageVariant = $page_variant;
  }

  /**
   * Returns the page executable for this event.
   *
   * @return \Drupal\page_manager\PageExecutable
   *   The page executable.
   */
  public function getPageExecutable() {
    return $this->page;
  }

}
