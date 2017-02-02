<?php

namespace Drupal\atm;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class AtmHttpClient. Client for API.
 */
class AtmHttpClient {

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
    return $this->getHelper()->get('base_url');
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
            "targetCb" => $this->getTargetCbJs(),
            "toggleCb" => $this->getToggleCbJs(),
          ],
          'content' => [
            'authorCb' => "function(onReady) {onReady({fullName: '$name', avatar: 'https://avatars.io/twitter/mitocgroup'})}",
            "container" => ".atm--node--view-mode--full",
            "selector" => "p",
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
            'main' => base64_encode('
              .atm-button {
                    line-height: 30px;
              }
            '),
          ],
        ],
      ],
    ]);

    if ($response->getStatusCode() == 200) {
      $_response = Json::decode($response->getBody()->getContents());

      $path_schema = file_default_scheme() . "://atm";

      /** @var \Drupal\Core\File\FileSystem $file_system */
      $file_system = \Drupal::service('file_system');

      $path = \Drupal::service('file_system')->realpath($path_schema);

      if (!is_dir($path)) {
        $file_system->mkdir($path, 0777, TRUE);
      }

      $script = file_get_contents($_response['BuildPath']);
      $script = gzdecode($script);

      file_put_contents($path . '/atm.min.js', $script);

      $url = file_create_url($path_schema . '/atm.min.js');

      $this->getHelper()->set('build_path', $url);
    }
  }

  /**
   * Get JS to targetCb function.
   *
   * @return string
   *   Return generated js.
   */
  public function getTargetCbJs() {
    $sticky = $this->getHelper()->get('target-cb-sticky');

    if (!$width = $this->getHelper()->get('target-cb-width')) {
      $width = '600px';
    }

    if (!$offset_top = $this->getHelper()->get('target-cb-offset-top')) {
      $offset_top = '0px';
    }

    if (!$offset_left = $this->getHelper()->get('target-cb-offset-left')) {
      $offset_left = '0px';
    }

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
      var mainModal=modalNode;
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
    $sticky = $this->getHelper()->get('target-cb-sticky');

    if (!$scrolling_offset_top = $this->getHelper()->get('target-cb-scrolling-offset-top')) {
      $scrolling_offset_top = 0;
    }

    if (!$sticky) {
      $scrolling_offset_top = -10;
    }

    return "function(cb) {
	  var adjustMarginTop = function (e) {
        var modalOffset = (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0) >= $scrolling_offset_top;
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
