<?php

/**
 * @file
 * Contains \Drupal\page_manager\Event\PageManagerContextEvent.
 */

namespace Drupal\page_manager\Event;

use Drupal\page_manager\PageInterface;
use Drupal\page_manager\Plugin\PageVariantInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps a page entity for event subscribers.
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
   * @param \Drupal\page_manager\PageInterface $page
   *   The page.
   * @param \Drupal\page_manager\Plugin\PageVariantInterface $page_variant
   *   (optional) The page variant.
   */
  public function __construct(PageInterface $page, PageVariantInterface $page_variant = NULL) {
    $this->page = $page;
    $this->pageVariant = $page_variant;
  }

  /**
   * Returns the page entity for this event.
   *
   * @return \Drupal\page_manager\PageInterface
   *   The page entity.
   */
  public function getPage() {
    return $this->page;
  }

}
