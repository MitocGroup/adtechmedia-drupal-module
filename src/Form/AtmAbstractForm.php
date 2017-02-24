<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class AtmAbstractForm.
 */
abstract class AtmAbstractForm extends FormBase {

  /**
   * Return AtmApiHelper.
   *
   * @return \Drupal\atm\Helper\AtmApiHelper
   *   Return AtmApiHelper.
   */
  protected function getHelper() {
    return \Drupal::service('atm.helper');
  }

  /**
   * Return AtmHttpClient.
   *
   * @return \Drupal\atm\AtmHttpClient
   *   Return AtmHttpClient.
   */
  protected function getAtmHttpClient() {
    return \Drupal::service('atm.http_client');
  }

  /**
   * Return lazy_builder for status message.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   Message text.
   *
   * @return array
   *   Return lazy_builder.
   */
  protected function getStatusMessage(TranslatableMarkup $message) {
    return $this->getMessage('status', $message);
  }

  /**
   * Return lazy_builder for warning message.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   Message text.
   *
   * @return array
   *   Return lazy_builder.
   */
  protected function getWarningMessage(TranslatableMarkup $message) {
    return $this->getMessage('warning', $message);
  }

  /**
   * Return lazy_builder for error message.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   Message text.
   *
   * @return array
   *   Return lazy_builder.
   */
  protected function getErrorMessage(TranslatableMarkup $message) {
    return $this->getMessage('error', $message);
  }

  /**
   * Return lazy_builder.
   *
   * @param string $type
   *   Type of message.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   Message.
   *
   * @return array
   *   Return lazy_builder.
   */
  private function getMessage($type, TranslatableMarkup $message) {
    $return = [
      '#theme' => 'status_messages',
      '#message_list' => [],
    ];

    $return['#message_list'][$type][] = $message;

    return $return;
  }

  /**
   * Convert `--` to `.` in element name.
   *
   * @param string $elementName
   *   Element name.
   *
   * @return mixed
   *   Element name.
   */
  protected function prepareElementName($elementName) {
    return str_replace('--', '.', $elementName);
  }

}
