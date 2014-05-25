<?php

/**
 * @file
 * Contains \Drupal\page_manager\PageInterface.
 */

namespace Drupal\page_manager;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginBagsInterface;

/**
 * Provides an interface for page entities.
 */
interface PageInterface extends ConfigEntityInterface, EntityWithPluginBagsInterface {

  /**
   * Returns the path for the page entity.
   *
   * @return string
   *   The path for the page entity.
   */
  public function getPath();

  /**
   * Indicates if this page is an admin page or not.
   *
   * @return bool
   *   TRUE if this is an admin page, FALSE otherwise.
   */
  public function usesAdminTheme();

  /**
   * Adds a new page variant to the page entity.
   *
   * @param array $configuration
   *   An array of configuration for the new page variant.
   *
   * @return string
   *   The page variant ID.
   */
  public function addPageVariant(array $configuration);

  /**
   * Retrieves a specific page variant.
   *
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return \Drupal\page_manager\Plugin\PageVariantInterface
   *   The page variant object.
   */
  public function getPageVariant($page_variant_id);

  /**
   * Removes a specific page variant.
   *
   * @param string $page_variant_id
   *   The page variant ID.
   *
   * @return $this
   */
  public function removePageVariant($page_variant_id);

  /**
   * Returns the page variants available for the page entity.
   *
   * @return \Drupal\page_manager\Plugin\PageVariantInterface[]
   *   An array of the page variants.
   */
  public function getPageVariants();

  /**
   * Selects the page variant to use for the page entity.
   *
   * This loops through the available page variants and checks each for access,
   * returning the first one that is accessible.
   *
   * @return \Drupal\page_manager\Plugin\PageVariantInterface|null
   *   Either the first accessible page variant, or NULL if none are accessible.
   */
  public function selectPageVariant();

  /**
   * Returns the conditions used for determining access for this page entity.
   *
   * @return \Drupal\Core\Condition\ConditionInterface[]|\Drupal\page_manager\Plugin\ConditionPluginBag
   *   An array of configured condition plugins.
   */
  public function getAccessConditions();

  /**
   * Adds a new access condition to the page entity.
   *
   * @param array $configuration
   *   An array of configuration for the new access condition.
   *
   * @return string
   *   The access condition ID.
   */
  public function addAccessCondition(array $configuration);

  /**
   * Retrieves a specific access condition.
   *
   * @param string $condition_id
   *   The access condition ID.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The access condition object.
   */
  public function getAccessCondition($condition_id);

  /**
   * Removes a specific access condition.
   *
   * @param string $condition_id
   *   The access condition ID.
   *
   * @return $this
   */
  public function removeAccessCondition($condition_id);

  /**
   * Returns the logic used to compute access, either 'and' or 'or'.
   *
   * @return string
   *   The string 'and', or the string 'or'.
   */
  public function getAccessLogic();

  /**
   * Gets the values for all defined contexts.
   *
   * @return \Drupal\Component\Plugin\Context\ContextInterface[]
   *   An array of set context values, keyed by context name.
   */
  public function getContexts();

  /**
   * Sets the context for a given name.
   *
   * @param string $name
   *   The name of the context.
   * @param \Drupal\Component\Plugin\Context\ContextInterface $value
   *   The context to add.
   *
   * @return $this
   */
  public function addContext($name, $value);

}
