<?php
/**
 * @file
 * Contains \Drupal\page_manager\Plugin\VariantAwareTrait.
 */

namespace Drupal\page_manager\Plugin;

/**
 * Provides methods for Page Manager entities that need to handle its variants.
 */
trait VariantAwareTrait {

  /**
   * The plugin bag that holds the variants.
   *
   * @var \Drupal\page_manager\Plugin\VariantBag
   */
  protected $variantBag;

  /**
   * @see \Drupal\page_manager\Plugin\VariantAwareInterface::addVariant()
   */
  public function addVariant(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getVariants()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * @see \Drupal\page_manager\Plugin\VariantAwareInterface::getVariant()
   */
  public function getVariant($variant_id) {
    return $this->getVariants()->get($variant_id);
  }

  /**
   * @see \Drupal\page_manager\Plugin\VariantAwareInterface::removeVariant()
   */
  public function removeVariant($variant_id) {
    $this->getVariants()->removeInstanceId($variant_id);
    return $this;
  }

  /**
   * @see \Drupal\page_manager\Plugin\VariantAwareInterface::getVariants()
   */
  public function getVariants() {
    if (!$this->variantBag) {
      $this->variantBag = new VariantBag(\Drupal::service('plugin.manager.display_variant'), $this->getVariantConfig());
      $this->variantBag->sort();
    }
    return $this->variantBag;
  }

  /**
   * Returns the configuration for stored variants.
   *
   * @return array
   *   An array of variant configuration, keyed by the unique variant ID.
   */
  abstract protected function getVariantConfig();

  /**
   * Returns the UUID generator.
   *
   * @return \Drupal\Component\Uuid\UuidInterface
   */
  abstract protected function uuidGenerator();

}
