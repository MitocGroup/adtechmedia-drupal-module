<?php

namespace Drupal\adtechmedia\Plugin\Field\FieldFormatter;

use Drupal\adtechmedia\AtmClient;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides helper methods to unlock content via ATM Service.
 */
trait AtmTextFormatterTrait {

  /**
   * Process text with ATM Service and apply locking algorithm.
   *
   * @param string $text
   *   The text to be processed.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity parent.
   *
   * @return string
   *   Processed text by scrambling or blurring it.
   */
  public static function atmContentProcess($text, EntityInterface $entity) {
    return $text;
  }

  /**
   * Add ATM JS to pages where content should be locked.
   *
   * @param array $elements
   *   Field elements.
   */
  public function applyAtmJs(&$elements) {
    if (\Drupal::config('adtechmedia.settings')->get('use_atm')) {
      // Add ATM.js library.
      $elements['#attached']['library'][] = 'adtechmedia/adtechmedia.atmjs';
    }
  }

}
