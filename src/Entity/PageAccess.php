<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\PageAccess.
 */

namespace Drupal\page_manager\Entity;

use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the page entity type.
 */
class PageAccess extends EntityAccessController {

  use ConditionAccessResolverTrait;

  /**
   * Wraps the context handler.
   *
   * @return \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected function contextHandler() {
    return \Drupal::service('context.handler');
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    /** @var $entity \Drupal\page_manager\PageInterface */
    if ($operation == 'view') {
      if (!$entity->status()) {
        return FALSE;
      }

      $conditions = $entity->getAccessConditions();
      $contexts = $entity->getExecutable()->getContexts();
      foreach ($conditions as $condition) {
        if ($condition instanceof ContextAwarePluginInterface) {
          $this->contextHandler()->applyContextMapping($condition, $contexts);
        }
      }
      return $this->resolveConditions($conditions, $entity->getAccessLogic(), $contexts);
    }
    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
