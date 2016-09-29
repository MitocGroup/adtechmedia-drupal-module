<?php

namespace Drupal\adtechmedia\Plugin\Field\FieldFormatter;

/**
 * Provides helper methods to unlock content via ATM Service.
 */
trait AtmTextFormatterTrait {

  /**
   * @todo lock the content and apply unlocking specific algorithm.
   */
  public static function atmContentLock($text) {
    $text = 'This content is locked. </br>' . $text;
    return $text;
  }

  /**
   *
   */
  public static function atmContentUnlock($text) {

    return $text;
  }

}
