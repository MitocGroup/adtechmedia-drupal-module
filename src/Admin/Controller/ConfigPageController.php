<?php

namespace Drupal\atm\Admin\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Provides routers for admin area controller atm.
 */
class ConfigPageController extends ControllerBase {

  /**
   * Return content for config page ATM.
   */
  public function content() {
    return [
      '#theme' => 'atm-admin-config-page',
      '#config_form' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('General Configuration'),
          'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmConfigForm'),
        ],
      ],

      '#api_key_info_form' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('Api Key Information'),
          'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmApiKeyInfo'),
        ],
      ],
    ];
  }
}