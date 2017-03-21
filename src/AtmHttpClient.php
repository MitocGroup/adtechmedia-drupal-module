<?php

namespace Drupal\atm;

use Drupal\Component\Serialization\Json;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

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
  private function sendRequest($path, $method, $params = [], $headers = []) {
    $try = 0;
    $tries = 5;

    $baseUrl = $this->getBaseUrl();

    if (empty($baseUrl)) {
      throw new AtmException(
        $this->t("Base url for request is empty. Please reinstall atm module.")
      );
    }

    $apiKey = $this->getHelper()->getApiKey();
    if ($apiKey) {
      $headers['X-Api-Key'] = $apiKey;
    }

    do {
      try {
        $options = [
          RequestOptions::HEADERS => $headers,
          RequestOptions::TIMEOUT => 15,
        ];

        if (count($params) > 0) {
          $options[RequestOptions::JSON] = $params;
        }

        $response = \Drupal::httpClient()->request($method, $baseUrl . $path, $options);

        return Json::decode($response->getBody()->getContents());
      }
      catch (ClientException $exception) {
        if ($try < $tries) {
          $try++; sleep(5);
          continue;
        }

        $responseError = Json::decode(
          $exception->getResponse()->getBody()->getContents()
        );

        throw new AtmException(
          $exception->getMessage() . PHP_EOL . 'X-Api-Key: ' . $apiKey . PHP_EOL . 'Response error: ' . json_encode($responseError, JSON_PRETTY_PRINT)
        );
      }
    } while (TRUE);
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

    return FALSE;
  }

  /**
   * Method provides request to API and return supported countries.
   */
  public function getPropertySupportedCountries() {
    try {
      $response = $this->sendRequest('/atm-admin/property/supported-countries', 'GET', []);
      return $response['Countries'];
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }

    return [];
  }

  /**
   * Method provides request to API for generating atm.js.
   */
  public function propertyCreate() {
    $website = $_SERVER['HTTP_HOST'];
    $name = $this->getHelper()->getApiName();
    $email = $this->getHelper()->getApiEmail();
    $country = $this->getHelper()->getApiCountry();

    $price = $this->getHelper()->get('price');
    $payment_pledged = $this->getHelper()->get('payment_pledged');
    $currency = $this->getHelper()->get('price_currency');
    $pledged_type = $this->getHelper()->get('pledged_type');

    $params = [
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
          'selector' => $this->getHelper()->get('selector'),
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
    ];

    try {
      $response = $this->sendRequest('/atm-admin/property/create', 'PUT', $params);

      $atmMinJS = $this->getHelper()->get('atm_js_local_file');
      $url = $this->getHelper()->saveBuildPath($response['BuildPath'], "://" . $atmMinJS);

      $this->getHelper()->set('build_path', $url);
      $this->getHelper()->set('property_id', $response['Id']);
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }
  }

  /**
   * Method provides request to API for update atm.js.
   */
  public function propertyUpdateConfig($templates = FALSE) {
    $price = $this->getHelper()->get('price');
    $payment_pledged = $this->getHelper()->get('payment_pledged');
    $currency = $this->getHelper()->get('price_currency');
    $pledged_type = $this->getHelper()->get('pledged_type');

    $params = [
      "Id" => $this->getHelper()->get('property_id'),
      'ConfigDefaults' => [
        "targetModal" => [
          "targetCb" => $this->getTargetCbJs(),
          "toggleCb" => $this->getToggleCbJs(),
        ],
        'content' => [
          'offset' => $this->getHelper()->get('content_offset'),
          'lock' => $this->getHelper()->get('content_lock'),
          'selector' => $this->getHelper()->get('selector'),
          'offsetType' => $this->getHelper()->get('content_offset_type'),
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
    ];

    if ($templates) {
      $params['ConfigDefaults']['templates'] = $templates;
    }

    try {
      $response = $this->sendRequest('/atm-admin/property/update-config', 'PATCH', $params);

      $atmMinJS = $this->getHelper()->get('atm_js_local_file');
      $url = $this->getHelper()->saveBuildPath($response['BuildPath'], "://" . $atmMinJS);
      $this->getHelper()->set('build_path', $url);
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }
  }

  /**
   * Get JS to targetCb function.
   *
   * @return string
   *   Return generated js.
   */
  public function getTargetCbJs() {
    $themeConfig = $this->getHelper()->getThemeConfig();

    $sticky = $themeConfig->get('sticky') !== NULL ? $themeConfig->get('sticky') : $this->getHelper()->get('styles.target-cb.sticky');
    $width = $themeConfig->get('width') !== NULL ? $themeConfig->get('width') : $this->getHelper()->get('styles.target-cb.width');
    $offsetTop = $themeConfig->get('offset-top') !== NULL ? $themeConfig->get('offset-top') : $this->getHelper()->get('styles.target-cb.offset-top');
    $offsetLeft = $themeConfig->get('offset-left') !== NULL ? $themeConfig->get('offset-left') : $this->getHelper()->get('styles.target-cb.offset-left');

    $content = '';

    if ($sticky) {
      $content .= "mainModal.rootNode.style.position = 'fixed';\n";
      $content .= "mainModal.rootNode.style.top = '$offsetTop';\n";
      $content .= "mainModal.rootNode.style.width = '$width';\n";
      $offsetLeft = trim($offsetLeft);
      $content .= "mainModal.rootNode.style.zIndex = 1;\n";

      if ('-' == $offsetLeft[0]) {
        $offsetLeft[0] = ' ';
        $content .= "mainModal.rootNode.style.left = 'calc(50% - $offsetLeft)';\n";
      }
      else {
        $content .= "mainModal.rootNode.style.left = 'calc(50% + $offsetLeft)';\n";
      }
      $content .= "mainModal.rootNode.style.transform = 'translateX(-50%)';\n";
    }
    else {
      $content .= "mainModal.rootNode.style.width = '100%';\n";
      $content .= "mainModal.rootNode.style.position = 'relative';\n";
      $content .= "mainModal.rootNode.style.zIndex = 1;\n";
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
    $themeConfig = $this->getHelper()->getThemeConfig();

    $sticky = $themeConfig->get('sticky') !== NULL ? $themeConfig->get('sticky') : $this->getHelper()->get('styles.target-cb.sticky');
    $scrollingOffsetTop = $themeConfig->get('scrolling-offset-top') !== NULL ? $themeConfig->get('scrolling-offset-top') : $this->getHelper()->get('styles.target-cb.scrolling-offset-top');
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

  /**
   * Generate Theme Config.
   */
  public function createThemeConfig() {

    /** @var \Drupal\Core\Extension\ThemeHandler $themeHandler */
    /** @var \Drupal\Core\Config\Config $themeConfig */

    $themeHandler = \Drupal::service('theme_handler');
    $defaultTheme = $themeHandler->getTheme($themeHandler->getDefault());
    $themeVersion = isset($defaultTheme->info['version']) ? $defaultTheme->info['version'] : \Drupal::VERSION;

    $params = [
      'ThemeId' => $defaultTheme->info['name'] . '@' . $themeVersion,
      'PropertyId' => $this->getHelper()->get('property_id'),
      'ThemeVersion' => $themeVersion,
      'ThemeName' => $defaultTheme->info['name'],
      'PlatformId' => 'Drupal8',
      'PlatformVersion' => \Drupal::VERSION,
      'ConfigName' => 'Drupal8@' . \Drupal::VERSION . '-' . $defaultTheme->info['name'] . '@' . $themeVersion,
      'Config' => $this->getHelper()->get('styles.target-cb'),
    ];

    try {
      $response = $this->sendRequest('/atm-admin/theme-config/create', 'PUT', $params);

      $this->getHelper()
        ->getThemeConfig(TRUE)
        ->set('theme-config-id', $response['Id'])
        ->save();
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }

  }

  /**
   * Update Theme Config.
   */
  public function updateThemeConfig() {
    $themeConfigId = $this->getHelper()->getThemeConfig()->get('theme-config-id');
    if (!$themeConfigId) {
      $this->createThemeConfig();
      return TRUE;
    }

    $params = [
      "Id" => $themeConfigId,
      "Config" => $this->getHelper()->get('styles.target-cb'),
    ];

    try {
      $this->sendRequest('/atm-admin/theme-config/update', 'POST', $params);
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }
  }

  /**
   * Retrieve Theme Config.
   */
  public function retrieveThemeConfig() {
    /** @var \Drupal\Core\Extension\ThemeHandler $themeHandler */

    $themeHandler = \Drupal::service('theme_handler');
    $defaultTheme = $themeHandler->getTheme($themeHandler->getDefault());

    $params = [
      'Theme' => $defaultTheme->info['name'],
    ];

    $requestPath = implode('?', [
      '/atm-admin/theme-config/retrieve', http_build_query($params),
    ]);

    try {
      $response = $this->sendRequest($requestPath, 'GET');

      if ($response) {
        $this->getHelper()
          ->getThemeConfig(TRUE)
          ->setData(array_merge(
            $response['Config'], ['theme-config-id' => $response['Id']]
          ))
          ->save();
        return TRUE;
      }
    }
    catch (AtmException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
    }
  }

}
