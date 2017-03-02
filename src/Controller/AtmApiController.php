<?php

namespace Drupal\atm\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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
    $jsPath = $helper->get('build_path');

    $https = &$_SERVER['HTTPS'];

    if ($https == 'on') {
      $jsPath = preg_replace("/^http/", 'https', $jsPath);
    }

    return new RedirectResponse($jsPath);
  }

  /**
   * Return service worker js.
   */
  public function getSwJs() {
    $https = &$_SERVER['HTTPS'];
    if ($https == 'on') {
      return new Response(
        file_get_contents('https://api.adtechmedia.io/atm-admin/atm-build/sw.min.js'),
        200,
        ['Content-Type' => 'application/javascript']
      );
    }

    return new Response();
  }

  /**
   * Return terms content.
   */
  public function getTerms() {
    $ajaxResponse = new JsonResponse();

    $cache = \Drupal::cache()->get('atm-terms');
    if ($cache) {
      $ajaxResponse->setData([
        'errors' => FALSE,
        'content' => $cache->data,
      ]);
    }
    else {
      try {
        $response = \Drupal::httpClient()->get('https://www.adtechmedia.io/terms/dialog.html');
        $content = $response->getBody()->getContents();

        \Drupal::cache()->set('atm-terms', $content);

        $ajaxResponse->setData([
          'errors' => FALSE,
          'content' => $content,
        ]);
      }
      catch (ClientException $exception) {
        $ajaxResponse->setData([
          'errors' => TRUE,
          'content' => $exception->getMessage(),
        ]);
      }
    }

    return $ajaxResponse;
  }

}
