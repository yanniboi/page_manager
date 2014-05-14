<?php

/**
 * @file
 * Contains \Drupal\block_page\Plugin\PageVariantBase.
 */

namespace Drupal\block_page\Plugin;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginDependencyTrait;

/**
 * @todo.
 */
abstract class PageVariantBase extends PluginBase implements PageVariantInterface {

  use PluginDependencyTrait;

  /**
   * The plugin bag that holds the block plugins.
   *
   * @var \Drupal\block_page\Plugin\BlockPluginBag
   */
  protected $pluginBag;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  protected function getBlockBag() {
    if (!$this->pluginBag) {
      $this->pluginBag = new BlockPluginBag(\Drupal::service('plugin.manager.block'), $this->configuration['blocks']);
    }
    return $this->pluginBag;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->configuration['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return (int) $this->configuration['weight'];
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->configuration['weight'] = (int) $weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array(
      'id' => $this->getPluginId(),
      'blocks' => $this->getBlockBag()->getConfiguration(),
    ) + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'label' => '',
      'uuid' => '',
      'weight' => 0,
      'blocks' => array(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    foreach ($this->getBlockBag() as $instance) {
      $this->calculatePluginDependencies($instance);
    }
    return $this->dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#description' => $this->t('The label for this page variant.'),
      '#default_value' => $this->label(),
      '#maxlength' => '255',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, array &$form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    $this->configuration['label'] = $form_state['values']['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getBlock($block_id) {
    return $this->getBlockBag()->get($block_id);
  }

  /**
   * {@inheritdoc}
   */
  public function addBlock(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getBlockBag()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function updateBlock($block_id, array $configuration) {
    $existing_configuration = $this->getBlock($block_id)->getConfiguration();
    $this->getBlockBag()->setInstanceConfiguration($block_id, $configuration + $existing_configuration);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionAssignment($block_id) {
    return $this->getBlockBag()->getBlockRegion($block_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionAssignments() {
    // Build an array of the region names in the right order.
    $empty = array_fill_keys(array_keys($this->getRegionNames()), array());
    $full = $this->getBlockBag()->getAllByRegion();
    // Merge it with the actual values to maintain the ordering.
    return array_intersect_key(array_merge($empty, $full), $empty);
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
    return count($this->configuration['blocks']);
  }

  /**
   * Returns the UUID generator.
   *
   * @return \Drupal\Component\Uuid\UuidInterface
   */
  protected function uuidGenerator() {
    return \Drupal::service('uuid');
  }

}
