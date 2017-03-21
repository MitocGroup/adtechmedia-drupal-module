<?php

namespace Drupal\atm\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    $isSecure = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443);

    if ($isSecure) {
      $jsPath = preg_replace("/^http/", 'https', $jsPath);
    }

    return new RedirectResponse($jsPath);
  }

  /**
   * Return service worker js.
   */
  public function getSwJs() {
    /** @var \Drupal\atm\Helper\AtmApiHelper $helper */
    $helper = \Drupal::service('atm.helper');

    $response = new Response('', 200, ['Content-Type' => 'application/javascript']);

    try {
      $httpResponse = \Drupal::httpClient()->get($helper->get('sw_js_file'));
      $response->setContent($httpResponse->getBody()->getContents());
    }
    catch (ClientException $e) {

    }

    return $response;
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
        /** @var \Drupal\atm\Helper\AtmApiHelper $helper */
        $helper = \Drupal::service('atm.helper');

        $response = \Drupal::httpClient()->get($helper->get('terms_dialog_url'));
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
