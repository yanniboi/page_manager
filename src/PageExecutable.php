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
class PageExecutable implements PageExecutableInterface {

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
   * {@inheritdoc}
   */
  public function getPage() {
    return $this->page;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function getContexts() {
    if (!$this->contexts) {
      $this->eventDispatcher()->dispatch(PageManagerEvents::PAGE_CONTEXT, new PageManagerContextEvent($this));
    }
    return $this->contexts;
  }

  /**
   * {@inheritdoc}
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
