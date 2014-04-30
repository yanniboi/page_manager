<?php

/**
 * @file
 * Contains \Drupal\block_group\Entity\BlockGroupListBuilder.
 */

namespace Drupal\block_group\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a list builder for block groups.
 */
class BlockGroupListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['regions'] = $this->t('Regions');
    $header['count'] = $this->t('Number of blocks');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity \Drupal\block_group\BlockGroupInterface */
    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();
    $row['regions'] = array('data' => array(
      '#theme' => 'item_list',
      '#items' => $entity->getRegionNames(),
    ));
    $row['count'] = $entity->getBlockCount();
    return $row + parent::buildRow($entity);
  }

}
