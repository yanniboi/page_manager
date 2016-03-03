<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\Page.
 */

namespace Drupal\page_manager\Entity;

use Drupal\page_manager\Event\PageManagerContextEvent;
use Drupal\page_manager\Event\PageManagerEvents;
use Drupal\page_manager\PageInterface;
use Drupal\panels\Entity\DisplayBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a Page entity class.
 *
 * @ConfigEntityType(
 *   id = "page",
 *   label = @Translation("Page"),
 *   handlers = {
 *     "access" = "Drupal\panels\Entity\DisplayAccess",
 *   },
 *   admin_permission = "administer pages",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "use_admin_theme",
 *     "path",
 *     "access_logic",
 *     "access_conditions",
 *     "parameters",
 *   },
 * )
 */
class Page extends DisplayBase implements PageInterface {
  /**
   * The path of the page entity.
   *
   * @var string
   */
  protected $path;

  /**
   * Indicates if this page should be displayed in the admin theme.
   *
   * @var bool
   */
  protected $use_admin_theme;

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * {@inheritdoc}
   */
  public function usesAdminTheme() {
    return isset($this->use_admin_theme) ? $this->use_admin_theme : strpos($this->getPath(), '/admin/') === 0;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    static::routeBuilder()->setRebuildNeeded();
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);
    static::routeBuilder()->setRebuildNeeded();
  }

  /**
   * Wraps the route builder.
   *
   * @return \Drupal\Core\Routing\RouteBuilderInterface
   *   An object for state storage.
   */
  protected static function routeBuilder() {
    return \Drupal::service('router.builder');
  }

  /**
   * {@inheritdoc}
   */
  public function getParameterNames() {
    if (preg_match_all('|\{(\w+)\}|', $this->getPath(), $matches)) {
      return $matches[1];
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    $this->filterParameters();
  }

  /**
   * Filters the parameters to remove any without a valid type.
   *
   * @return $this
   */
  protected function filterParameters() {
    foreach ($this->getParameters() as $name => $parameter) {
      if (empty($parameter['type'])) {
        $this->removeParameter($name);
      }
    }
    return $this;
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
   * Wraps the event dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event dispatcher.
   */
  protected function eventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

}
