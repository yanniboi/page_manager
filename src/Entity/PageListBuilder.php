<?php

/**
 * @file
 * Contains \Drupal\page_manager\Entity\PageListBuilder.
 */

namespace Drupal\page_manager\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a list builder for page entities.
 */
class PageListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['path'] = $this->t('Path');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity \Drupal\page_manager\PageInterface */
    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();
    $row['path']['data'] = array(
      '#type' => 'link',
      '#href' => $entity->getPath(),
      '#title' => $entity->getPath(),
    );
    return $row + parent::buildRow($entity);
  }

}
