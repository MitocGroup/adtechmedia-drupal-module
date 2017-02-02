<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormBase;

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

}
