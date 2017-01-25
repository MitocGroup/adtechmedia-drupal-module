<?php

namespace Drupal\atm\Controller;

use Drupal\atm\Helper\AtmApiHelper;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides routers for controller atm.
 */
class AtmApiController extends ControllerBase {

  /**
   * Redirect to atm.js.
   */
  public function getJs() {
    /** @var AtmApiHelper $helper */
    $helper = \Drupal::service('atm.helper');

    return new RedirectResponse(
      $helper->get('build_path')
    );
  }
}