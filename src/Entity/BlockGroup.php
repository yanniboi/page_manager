<?php

/**
 * @file
 * Contains \Drupal\block_group\Entity\BlockGroup.
 */

namespace Drupal\block_group\Entity;

use Drupal\block_group\BlockGroupInterface;
use Drupal\block_group\BlockPluginBag;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\EntityWithPluginBagInterface;

/**
 * Defines a Block Group entity class.
 *
 * @ConfigEntityType(
 *   id = "block_group",
 *   label = @Translation("Block Group"),
 *   controllers = {
 *     "list_builder" = "Drupal\block_group\Entity\BlockGroupListBuilder",
 *     "view_builder" = "Drupal\block_group\Entity\BlockGroupViewBuilder",
 *     "access" = "Drupal\block_group\Entity\BlockGroupAccessController",
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
class BlockGroup extends ConfigEntityBase implements EntityWithPluginBagInterface, BlockGroupInterface {

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
   * The array of block configurations.
   *
   * @var array
   */
  protected $blocks = array();

  /**
   * {@inheritdoc}
   */
  protected $pluginConfigKey = 'blocks';

  /**
   * The plugin bag that holds the block plugins.
   *
   * @var \Drupal\Component\Plugin\PluginBag
   */
  protected $pluginBag;

  /**
   * The regions used by this block group.
   *
   * @todo Replace with a reference to a Layout object.
   *
   * @var array
   */
  protected $regions = array(
    'top' => 'Top',
    'bottom' => 'Bottom',
  );

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $properties = parent::toArray();
    $names = array(
      'id',
      'label',
      'regions',
      $this->pluginConfigKey,
    );
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginBag() {
    if (!$this->pluginBag) {
      $this->pluginBag = new BlockPluginBag(\Drupal::service('plugin.manager.block'), $this->get($this->pluginConfigKey));
    }
    return $this->pluginBag;
  }

  /**
   * {@inheritdoc}
   */
  public function addBlockToGroup(array $configuration) {
    $uuid = $this->uuidGenerator()->generate();
    $this->getPluginBag()->addInstanceId($uuid, $configuration);
    return $uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionAssignment($block_id) {
    return $this->getPluginBag()->getBlockRegion($block_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionAssignments() {
    return $this->getPluginBag()->getAllByRegion() + array_fill_keys(array_keys($this->getRegionNames()), array());
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionAssignment($block_id, $region) {
    $this->getPluginBag()->setBlockRegion($block_id, $region);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionNames() {
    return $this->get('regions');
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionName($region) {
    $regions = $this->getRegionNames();
    return isset($regions[$region]) ? $regions[$region] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockCount() {
    return count($this->get('blocks'));
  }

}
