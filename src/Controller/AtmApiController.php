<?php

namespace Drupal\atm\Controller;

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
    /** @var \Drupal\atm\Helper\AtmApiHelper $helper */
    $helper = \Drupal::service('atm.helper');

    return new RedirectResponse(
      $helper->get('build_path')
    );
  }

}
