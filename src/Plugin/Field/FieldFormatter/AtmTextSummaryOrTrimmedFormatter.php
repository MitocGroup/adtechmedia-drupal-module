<?php

namespace Drupal\AdTechMedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\text\Plugin\Field\FieldFormatter\TextSummaryOrTrimmedFormatter;
use Drupal\adtechmedia\Plugin\Field\FieldFormatter\AtmTextFormatterTrait;

/**
 *
 */
class AtmTextSummaryOrTrimmedFormatter extends TextSummaryOrTrimmedFormatter {

  use AtmTextFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    // @todo figure out if field is visible, view mode is full (exclude teaser)
    foreach ($elements as $key => &$element) {
      $element['#text'] = self::atmContentLock($element['#text']);
    }

    return $elements;
  }

}
