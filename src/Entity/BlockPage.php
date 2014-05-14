<?php

/**
 * @file
 * Contains \Drupal\block_page\Entity\BlockPage.
 */

namespace Drupal\block_page\Entity;

use Drupal\block_page\BlockPageInterface;
use Drupal\block_page\Plugin\PageVariantBag;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a Block Page entity class.
 *
 * @ConfigEntityType(
 *   id = "block_page",
 *   label = @Translation("Block Page"),
 *   controllers = {
 *     "list_builder" = "Drupal\block_page\Entity\BlockPageListBuilder",
 *     "view_builder" = "Drupal\block_page\Entity\BlockPageViewBuilder",
 *     "form" = {
 *       "add" = "Drupal\block_page\Form\BlockPageAddForm",
 *       "edit" = "Drupal\block_page\Form\BlockPageEditForm",
 *       "delete" = "Drupal\block_page\Form\BlockPageDeleteForm",
 *     }
 *   },
 *   admin_permission = "administer block pages",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "block_page.page_add",
 *     "edit-form" = "block_page.page_edit",
 *     "delete-form" = "block_page.page_delete",
 *   }
 * )
 */
class BlockPage extends ConfigEntityBase implements BlockPageInterface {

  /**
   * The ID of the block page.
   *
   * @var string
   */
  protected $id;

  /**
   * The label of the block page.
   *
   * @var string
   */
  protected $label;

  /**
   * The path of the block page.
   *
   * @var string
   */
  protected $path;

  /**
   * @todo.
   *
   * @var array
   */
  protected $page_variants = array();

  /**
   * {@inheritdoc}
   */
  protected $pluginConfigKey = 'page_variants';

  /**
   * The plugin bag that holds the page variants.
   *
   * @var \Drupal\Component\Plugin\PluginBag
   */
  protected $pluginBag;

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $properties = parent::toArray();
    $names = array(
      'id',
      'label',
      'path',
      'page_variants',
    );
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * {@inheritdoc}
   */
  public function postCreate(EntityStorageInterface $storage) {
    parent::postCreate($storage);
    // Ensure there is at least one page variant.
    if (!$this->getPluginBag()->count()) {
      $this->addPageVariant(array(
        'id' => 'default',
        'label' => 'Default',
      ));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    $this->routeBuilder()->setRebuildNeeded();
  }

  /**
   * Wraps the route builder.
   *
   * @return \Drupal\Core\Routing\RouteBuilderInterface
   *   An object for state storage.
   */
  protected function routeBuilder() {
    return \Drupal::service('router.builder');
  }

  /**
   * {@inheritdoc}
   */
  public function addPageVariant(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getPluginBag()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPageVariant($page_variant_id) {
    return $this->getPluginBag()->get($page_variant_id);
  }

  /**
   * {@inheritdoc}
   */
  public function removePageVariant($page_variant_id) {
    $this->getPluginBag()->removeInstanceId($page_variant_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginBag() {
    if (!$this->pluginBag) {
      $this->pluginBag = new PageVariantBag(\Drupal::service('plugin.manager.page_variant'), $this->get($this->pluginConfigKey));
      $this->pluginBag->sort();
    }
    return $this->pluginBag;
  }

  /**
   * {@inheritdoc}
   */
  public function selectPageVariant() {
    foreach ($this->getPluginBag() as $page_variant) {
      /** @var $page_variant \Drupal\block_page\Plugin\PageVariantInterface */
      if ($page_variant->access()) {
        return $page_variant;
      }
    }
    return NULL;
  }

}
