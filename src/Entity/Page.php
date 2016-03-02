<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\Page.
 */

namespace Drupal\page_manager\Entity;

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
 *     "access" = "Drupal\page_manager\Entity\PageAccess",
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
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function getParameter($name) {
    if (!isset($this->parameters[$name])) {
      $this->setParameter($name, '');
    }
    return $this->parameters[$name];
  }

  /**
   * {@inheritdoc}
   */
  public function setParameter($name, $type, $label = '') {
    $this->parameters[$name] = [
      'machine_name' => $name,
      'type' => $type,
      'label' => $label,
    ];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeParameter($name) {
    unset($this->parameters[$name]);
    return $this;
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
   * Wraps the event dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event dispatcher.
   */
  protected function eventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

}
