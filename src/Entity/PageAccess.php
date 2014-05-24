<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\PageAccess.
 */

namespace Drupal\page_manager\Entity;

use Drupal\page_manager\Plugin\ConditionAccessResolverTrait;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the page entity type.
 */
class PageAccess extends EntityAccessController {

  use ConditionAccessResolverTrait;

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    /** @var $entity \Drupal\page_manager\PageInterface */
    if ($operation == 'view') {
      if (!$entity->status()) {
        return FALSE;
      }

      return $this->resolveConditions($entity->getAccessConditions(), $entity->getAccessLogic(), $entity->getContexts());
    }
    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
