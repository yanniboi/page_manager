<?php

/**
 * @file
 * Contains \Drupal\page_manager\Form\AjaxFormTrait.
 */

namespace Drupal\page_manager\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides helper methods for using an AJAX modal.
 */
trait AjaxFormTrait {

  /**
   * Gets attributes for use with an AJAX modal.
   *
   * @return array
   */
  protected function getAjaxAttributes() {
    return [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode([
        'width' => 'auto',
      ]),
    ];
  }

  /**
   * Gets attributes for use with an add button AJAX modal.
   *
   * @return array
   */
  protected function getAjaxButtonAttributes() {
    return NestedArray::mergeDeep($this->getAjaxAttributes(), [
      'class' => [
        'button',
        'button--small',
        'button-action',
      ],
    ]);
  }

}
