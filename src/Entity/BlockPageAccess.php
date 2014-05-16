<?php

/**
 * @file
 * Contains \Drupal\block_page\Entity\BlockPageAccess.
 */

namespace Drupal\block_page\Entity;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the block page entity type.
 */
class BlockPageAccess extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    /** @var $entity \Drupal\block_page\BlockPageInterface */
    if ($operation == 'view') {
      foreach ($entity->getAccessConditions() as $access_condition) {
        try {
          if (!$access_condition->execute()) {
            // If any access condition fails, prevent access.
            return FALSE;
          }
        }
        catch (PluginException $e) {
          // A missing context should always deny access.
          return FALSE;
        }
      }
      return TRUE;
    }
    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
