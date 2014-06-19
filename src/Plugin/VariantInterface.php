<?php

/**
 * @file
 * Contains \Drupal\page_manager\Plugin\VariantInterface.
 */

namespace Drupal\page_manager\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface for DisplayVariant plugins.
 */
interface VariantInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the user-facing display variant label.
   *
   * @return string
   *   The display variant label.
   */
  public function label();

  /**
   * Returns the admin-facing display variant label.
   *
   * This is for the type of display variant, not the configured variant itself.
   *
   * @return string
   *   The display variant administrative label.
   */
  public function adminLabel();

  /**
   * Returns the unique ID for the display variant.
   *
   * @return string
   *   The display variant ID.
   */
  public function id();

  /**
   * Returns the weight of the display variant.
   *
   * @return int
   *   The display variant weight.
   */
  public function getWeight();

  /**
   * Sets the weight of the display variant.
   *
   * @param int $weight
   *   The weight to set.
   */
  public function setWeight($weight);

  /**
   * Determines if this display variant is accessible.
   *
   * @return bool
   *   TRUE if this display variant is accessible, FALSE otherwise.
   */
  public function access();

  /**
   * Returns the render array for the display variant.
   *
   * @return array
   *   A render array for the display variant.
   */
  public function render();

}
