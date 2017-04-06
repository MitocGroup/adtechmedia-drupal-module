<?php

namespace Drupal\atm;

use Drupal\Component\Serialization\Json;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class AtmHttpClient. Client for API.
 */
class AtmHttpClient {

  use StringTranslationTrait;

  /**
   * Return AtmApiHelper.
   *
   * @return \Drupal\atm\Helper\AtmApiHelper
   *   Return AtmApiHelper.
   */
  private function getHelper() {
    return \Drupal::service('atm.helper');
  }

  /**
   * Return base url for api.
   */
  public function getBaseUrl() {
    return $this->getHelper()->get('base_endpoint');
  }

  /**
   * Method provides request to API.
   */
  private function sendRequest($path, $method, $params, $headers = []) {
    $client = new Client();

    $baseUrl = $this->getBaseUrl();

    if (empty($baseUrl)) {
      throw new AtmException(
        $this->t("Base url for request is empty. Please reinstall atm module.")
      );
    }

    $response = $client->request($method, $baseUrl . $path, [
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
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }
    catch (ClientException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }

    return FALSE;
  }

  /**
   * Method provides request to API and return supported countries.
   */
  public function getPropertySupportedCountries() {
    $client = new Client();

    $api_key = $this->getHelper()->getApiKey();

    try {
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
    }
    catch (ClientException $exception) {
      drupal_set_message($exception->getMessage() . PHP_EOL . 'X-Api-Key:' . $api_key, 'error');
    }

    return [];
  }

  /**
   * Method provides request to API for generating atm.js.
   */
  public function propertyCreate() {
    $client = new Client();

    $website = $_SERVER['HTTP_HOST'];
    $name = $this->getHelper()->getApiName();
    $email = $this->getHelper()->getApiEmail();
    $country = $this->getHelper()->getApiCountry();

    $price = $this->getHelper()->get('price');
    $payment_pledged = $this->getHelper()->get('payment_pledged');
    $currency = $this->getHelper()->get('price_currency');
    $pledged_type = $this->getHelper()->get('pledged_type');

    $options = [
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
            "targetCb" => $this->getTargetCbJs(),
            "toggleCb" => $this->getToggleCbJs(),
          ],
          'content' => [
            'authorCb' => "function(onReady) {onReady({fullName: '$name', avatar: 'https://avatars.io/twitter/mitocgroup'})}",
            "container" => ".atm--node--view-mode--full",
            'offset' => $this->getHelper()->get('content_offset'),
            'lock' => $this->getHelper()->get('content_lock'),
            'offsetType' => $this->getHelper()->get('content_offset_type'),
          ],
          'revenueMethod' => 'micropayments',
          'ads' => [
            'relatedVideoCb' => "function (onReady) { }",
          ],
          'payment' => [
            'price' => $price,
            'pledged' => $payment_pledged,
            'currency' => $currency,
            'pledgedType' => $pledged_type,
          ],
          'styles' => [
            'main' => base64_encode(
              $this->getHelper()->getTemplateOwerallStyles()
            ),
          ],
        ],
      ],
    ];

    try {
      $response = $client->put($this->getBaseUrl() . '/atm-admin/property/create', $options);
      $_response = Json::decode($response->getBody()->getContents());

      $atmMinJS = $this->getHelper()->get('atm_js_local_file');

      $url = $this->getHelper()->saveBuildPath($_response['BuildPath'], "://" . $atmMinJS);

      $this->getHelper()->set('build_path', $url);
      $this->getHelper()->set('property_id', $_response['Id']);
    }
    catch (ClientException $exception) {
      $responseError = Json::decode(
        $exception->getResponse()->getBody()->getContents()
      );

      $json = json_encode($responseError, JSON_PRETTY_PRINT);

      drupal_set_message(
        $exception->getMessage() . PHP_EOL .
        'X-Api-Key: ' . $this->getHelper()->getApiKey() . PHP_EOL .
        'Response error: ' . $json, 'error');
    }
  }

  /**
   * Method provides request to API for update atm.js.
   */
  public function propertyUpdateConfig($templates) {
    $client = new Client();

    $options = [
      'headers' => [
        'X-Api-Key' => $this->getHelper()->getApiKey(),
      ],

      'json' => [
        "Id" => $this->getHelper()->get('property_id'),
        'ConfigDefaults' => [
          "templates" => $templates,
          "targetModal" => [
            "targetCb" => $this->getTargetCbJs(),
            "toggleCb" => $this->getToggleCbJs(),
          ],
          'styles' => [
            'main' => base64_encode(
              $this->getHelper()->getTemplateOwerallStyles()
            ),
          ],
        ],
      ],
    ];

    try {
      $response = $client->patch($this->getBaseUrl() . '/atm-admin/property/update-config', $options);
      $_response = Json::decode($response->getBody()->getContents());

      $url = $this->getHelper()->saveBuildPath($_response['BuildPath'], "://atm/atm.min.js");
      $this->getHelper()->set('build_path', $url);
    }
    catch (ClientException $exception) {
      drupal_set_message($exception->getMessage() . PHP_EOL . 'X-Api-Key:' . $this->getHelper()->getApiKey(), 'error');
    }
  }

  /**
   * Get JS to targetCb function.
   *
   * @return string
   *   Return generated js.
   */
  public function getTargetCbJs() {
    $sticky = $this->getHelper()->get('styles.target-cb.sticky');

    $width = $this->getHelper()->get('styles.target-cb.width');
    $offset_top = $this->getHelper()->get('styles.target-cb.offset-top');
    $offset_left = $this->getHelper()->get('styles.target-cb.offset-left');

    $content = '';

    if ($sticky) {
      $content .= "mainModal.rootNode.style.position = 'fixed';\n";
      $content .= "mainModal.rootNode.style.top = '$offset_top';\n";
      $content .= "mainModal.rootNode.style.width = '$width';\n";
      $offset_left = trim($offset_left);

      if ('-' == $offset_left[0]) {
        $offset_left[0] = ' ';
        $content .= "mainModal.rootNode.style.left = 'calc(50% - $offset_left)';\n";
      }
      else {
        $content .= "mainModal.rootNode.style.left = 'calc(50% + $offset_left)';\n";
      }
      $content .= "mainModal.rootNode.style.transform = 'translateX(-50%)';\n";
    }
    else {
      $content .= "mainModal.rootNode.style.width = '100%';\n";
    }

    return "function(modalNode, cb) {
      var mainModal = modalNode;
      mainModal.mount(
        document.getElementById('atm-modal-content'), mainModal.constructor.MOUNT_APPEND
      );
      mainModal.rootNode.classList.add('atm-targeted-container');
      $content
      cb();
    }";
  }

  /**
   * Get JS to toggleCb function.
   *
   * @return string
   *   Return generated js.
   */
  public function getToggleCbJs() {
    $sticky = $this->getHelper()->get('styles.target-cb.sticky');

    $scrollingOffsetTop = $this->getHelper()->get('styles.target-cb.scrolling-offset-top');
    $scrollingOffsetTop *= 1;

    if (!$sticky) {
      $scrollingOffsetTop = -10;
    }

    return "function(cb) {
	  var adjustMarginTop = function (e) {
        var modalOffset = (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0) >= $scrollingOffsetTop;
        if (modalOffset) {
          cb(true);
        } else {
          cb(false);
        }
      };
      document.addEventListener('scroll', adjustMarginTop);
      adjustMarginTop(null);
    }";
  }

}
