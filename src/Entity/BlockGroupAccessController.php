<?php

/**
 * @file
 * Contains \Drupal\block_group\Entity\BlockGroupAccessController.
 */

namespace Drupal\block_group\Entity;

use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * @todo.
 */
class BlockGroupAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    if ($operation != 'view') {
      return parent::checkAccess($entity, $operation, $langcode, $account);
    }

    // If any blocks allow access, allow access to the whole group. The
    // per-block access is handled in the view builder.
    foreach ($entity->getPluginBag() as $block) {
      if ($block->access($account)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
