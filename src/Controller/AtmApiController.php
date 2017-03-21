<?php

namespace Drupal\atm\Controller;

use Drupal\atm\Helper\AtmApiHelper;
use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Provides routers for controller atm.
 */
class AtmApiController extends ControllerBase {

  /**
   * Helper for ATM.
   *
   * @var \Drupal\atm\Helper\AtmApiHelper
   */
  private $atmApiHelper;

  /**
   * Http client.
   *
   * @var \GuzzleHttp\Client
   */
  private $httpClient;

  /**
   * AtmApiController constructor.
   *
   * @param \Drupal\atm\Helper\AtmApiHelper $atmApiHelper
   *   Helper for ATM.
   * @param \GuzzleHttp\Client $httpClient
   *   Http client.
   */
  public function __construct(AtmApiHelper $atmApiHelper, Client $httpClient) {
    $this->atmApiHelper = $atmApiHelper;
    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('atm.helper'),
      $container->get('http_client')
    );
  }

  /**
   * Return helper for ATM.
   *
   * @return \Drupal\atm\Helper\AtmApiHelper
   *   Helper for ATM.
   */
  public function getAtmApiHelper() {
    return $this->atmApiHelper;
  }

  /**
   * Return Http client.
   *
   * @return \GuzzleHttp\Client
   *   http client.
   */
  public function getHttpClient() {
    return $this->httpClient;
  }

  /**
   * Redirect to atm.js.
   */
  public function getJs() {
    $jsPath = $this->getAtmApiHelper()->get('build_path');

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
    $response = new Response('', 200, ['Content-Type' => 'application/javascript']);

    try {
      $httpResponse = $this->getHttpClient()->get($this->getAtmApiHelper()->get('sw_js_file'));
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
        $response = $this->getHttpClient()->get($this->getAtmApiHelper()->get('terms_dialog_url'));
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
