<?php

namespace Drupal\atm;

use Drupal\atm\Helper\AtmApiHelper;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AtmHttpClient {

  /**
   * Return AtmApiHelper.
   *
   * @return AtmApiHelper
   *   Return AtmApiHelper.
   */
  private function getHelper() {
    return \Drupal::service('atm.helper');
  }

  /**
   * Return base url for api.
   */
  public function getBaseUrl() {
    $base_url = \Drupal::configFactory()->getEditable('atm.settings')->get('base_url');

    if (empty($base_url)) {
      $base_url = 'https://api.adtechmedia.io/prod';
    }

    return $base_url;
  }

  /**
   * Method provides request to API.
   */
  private function sendRequest($path, $method, $params, $headers = []) {
    $client = new Client();

    $response = $client->request($method, $this->getBaseUrl() . $path, [
      'json' => $params,
    ]);

    return Json::decode($response->getBody()->getContents());
  }

  /**
   * Method provides request to API for generate api-key.
   */
  public function generateApiKey($name, $hostname = FALSE) {
    try {
      $params = [
        'Name' => $name,
      ];

      if ($hostname) {
        $params['Hostname'] = $_SERVER['HTTP_HOST'];
      }

      $body = $this->sendRequest('/atm-admin/api-gateway-key/create', 'PUT', $params);

      return $body['Key'];
    }
    catch (ClientException $e) {

    }

    return FALSE;
  }

  /**
   * Method provides request to API and return supported countries.
   */
  public function getPropertySupportedCountries() {
    $client = new Client();

    $api_key = $this->getHelper()->getApiKey();

    $response = $client->get($this->getBaseUrl() . '/atm-admin/property/supported-countries', [
      'headers' => [
        'X-Api-Key' => $api_key,
      ],
    ]);

    if ($response->getStatusCode() == 200) {
      $body = Json::decode(
          $response->getBody()->getContents()
      );

      return $body['Countries'];
    }

    return [];
  }

  /**
   * Method provides request to API for generating atm.js.
   */
  public function propertyCreate($name, $website, $email, $country) {
    $client = new Client();

    $response = $client->put($this->getBaseUrl() . '/atm-admin/property/create', [
      'headers' => [
        'X-Api-Key' => $this->getHelper()->getApiKey(),
      ],

      'json' => [
        'Name' => $name,
        'Website' => $website,
        'SupportEmail' => $email,
        'Country' => $country,
        'ConfigDefaults' => [
          "targetModal" => [
            "targetCb" => "function(modalNode, cb) {
                var mainModal = modalNode;
                mainModal.mount(
                  document.getElementById('atm-modal-content'), mainModal.constructor.MOUNT_APPEND
                );
                cb();
              }",
            "toggleCb" => "function(cb) { cb(true) }",
          ],
          'content' => [
            'authorCb' => "function(onReady) {onReady({fullName: '$name', avatar: 'https://avatars.io/twitter/mitocgroup'})}",
            "container" => ".atm--node--view-mode--full",
            "selector" => "p",
          ],
        ],
      ],
    ]);

    if ($response->getStatusCode() == 200) {
      return Json::decode($response->getBody()->getContents());
    }

    return FALSE;
  }
}