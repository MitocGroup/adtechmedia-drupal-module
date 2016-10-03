<?php

namespace Drupal\adtechmedia;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * AdTechMedia Guzzle client.
 */
class AtmClient extends Client {

  /**
   * AdTechMedia Service URI.
   *
   * @var string
   */
  const ATM_HOST = 'https://atm-dev.adtechmedia.io';

  /**
   * AdTechMedia Development Service URI.
   *
   * @var string
   */
  const ATM_DEV_HOST = 'https://atm-dev.adtechmedia.io';

  /**
   * AdTechMedia base path.
   *
   * @var string
   */
  const ATM_BASE_PATH = 'dev';

  /**
   * AdTechMedia active host.
   *
   * @var string
   */
  private $atmHost;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $config = []) {
    $atm_conf = \Drupal::config('adtechmedia.admin_settings');

    $this->atmHost = self::ATM_HOST;

    if ($atm_conf->get('development')) {
      $this->atmHost = self::ATM_DEV_HOST;
    }

    $config['base_uri'] = $this->atmHost;
    parent::__construct($config);
  }

  /**
   * PUT request to regenerate ATM API key.
   *
   * @return mixed|null
   *   Request content response or NULL.
   */
  public function regenerateApiKey() {
    $client = new Client($this->getConfig());

    try {
      // Generate ATM API key.
      $request = $client->put($this->atmHost . '/' . self::ATM_BASE_PATH . '/atm-admin/api-gateway-key/create', [
        'json' => [
          'Name' => 'Test',
      // 'Hostname' =>  '',
      //          'Ip' => \Drupal::requestStack()->getCurrentRequest()->getClientIp(),
      //          'Platform' =>  '',
      //          'PlatformVersion' =>  '',
      //          'PluginVersion' =>  '',.
        ],
      ]);

      $response = Json::decode($request->getBody()->getContents());

      return $response;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return NULL;
  }

}
