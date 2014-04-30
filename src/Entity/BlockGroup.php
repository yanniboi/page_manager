<?php

/**
 * @file
 * Contains \Drupal\block_group\Entity\BlockGroup.
 */

namespace Drupal\block_group\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines a Block Group entity class.
 *
 * @ConfigEntityType(
 *   id = "block_group",
 *   label = @Translation("Block Group"),
 *   controllers = {
 *     "list_builder" = "Drupal\block_group\Entity\BlockGroupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\block_group\Form\BlockGroupAddForm",
 *       "edit" = "Drupal\block_group\Form\BlockGroupEditForm",
 *       "delete" = "Drupal\block_group\Form\BlockGroupDeleteForm",
 *     }
 *   },
 *   admin_permission = "administer block groups",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "block_group.add",
 *     "edit-form" = "block_group.edit",
 *     "delete-form" = "block_group.delete",
 *   }
 * )
 */
class BlockGroup extends ConfigEntityBase {

  /**
   * The ID of the block group.
   *
   * @var string
   */
  protected $id;

  /**
   * The label of the block group.
   *
   * @var string
   */
  protected $label;

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $properties = parent::toArray();
    $names = array(
      'id',
      'label',
    );
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

}
