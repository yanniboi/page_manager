<?php

/**
 * @file
 * Contains \Drupal\page_manager\PageExecutable.
 */

namespace Drupal\page_manager;

use Drupal\page_manager\Event\PageManagerContextEvent;
use Drupal\page_manager\Event\PageManagerEvents;

/**
 * Represents a page entity during runtime execution.
 */
class PageExecutable {

  /**
   * The page entity.
   *
   * @var \Drupal\page_manager\PageInterface
   */
  protected $page;

  /**
   * The selected page variant.
   *
   * @var \Drupal\page_manager\Plugin\PageVariantInterface|null
   */
  protected $selectedPageVariant;

  /**
   * An array of collected contexts.
   *
   * @var \Drupal\Component\Plugin\Context\ContextInterface[]
   */
  protected $contexts = array();

  /**
   * Constructs a new PageExecutable.
   *
   * @param \Drupal\page_manager\PageInterface $page
   *   The page entity.
   */
  public function __construct(PageInterface $page) {
    $this->page = $page;
  }

  /**
   * Retrieves the underlying page entity.
   *
   * @return \Drupal\page_manager\PageInterface
   *   The page entity.
   */
  public function getPage() {
    return $this->page;
  }

  /**
   * Selects the page variant to use for the page entity.
   *
   * This loops through the available page variants and checks each for access,
   * returning the first one that is accessible.
   *
   * @return \Drupal\page_manager\Plugin\PageVariantInterface|null
   *   Either the first accessible page variant, or NULL if none are accessible.
   */
  public function selectPageVariant() {
    if (!$this->selectedPageVariant) {
      foreach ($this->page->getPageVariants() as $page_variant) {
        $page_variant->setContexts($this->getContexts());
        if ($page_variant->access()) {
          $this->selectedPageVariant = $page_variant->init($this);
          break;
        }
      }
    }
    return $this->selectedPageVariant;
  }

  /**
   * Gets the values for all defined contexts.
   *
   * @return \Drupal\Component\Plugin\Context\ContextInterface[]
   *   An array of set context values, keyed by context name.
   */
  public function getContexts() {
    if (!$this->contexts) {
      $this->eventDispatcher()->dispatch(PageManagerEvents::PAGE_CONTEXT, new PageManagerContextEvent($this));
    }
    return $this->contexts;
  }

  /**
   * Sets the context for a given name.
   *
   * @param string $name
   *   The name of the context.
   * @param \Drupal\Component\Plugin\Context\ContextInterface $value
   *   The context to add.
   *
   * @return $this
   */
  public function addContext($name, $value) {
    $this->contexts[$name] = $value;
  }

  /**
   * Wraps the event dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event dispatcher.
   */
  protected function eventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

}
