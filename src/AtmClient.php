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
   * Request options.
   *
   * @var array
   */
  private $options;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $config = []) {
    $atm_conf = \Drupal::config('adtechmedia.settings');
    $atm_dev_conf = \Drupal::config('adtechmedia.admin_settings');

    $this->atmHost = self::ATM_HOST . '/' . self::ATM_BASE_PATH;

    if ($atm_dev_conf->get('development')) {
      $this->atmHost = self::ATM_DEV_HOST;
      $config['debug'] = TRUE;
    }

    $this->setHeader('X-Api-Key', $atm_conf->get('api_key'));

    $config['base_uri'] = $this->atmHost;
    $config['headers']['X-Api-Key'] = $atm_conf->get('api_key');
    $config['debug'] = TRUE;

    parent::__construct($config);
  }

  /**
   * Set request header.
   *
   * @param string $name
   *   Header property name.
   * @param string $value
   *   Header property value.
   */
  private function setHeader($name, $value) {
    $this->options['headers'][$name] = $value;
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
      $request = $client->put($this->atmHost . '/atm-admin/api-gateway-key/create', [
        'json' => [
          'Name' => 'Drupal',
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

  /**
   * Retrieve locked content from ATM api.
   *
   * @param string $content_id
   *   Content identifier.
   * @param $property_id
   *   Property identifier.
   *
   * @return mixed|null
   *   Request content response or FALSE on error.
   */
  public function retrieveLockedContent($content_id, $property_id) {
    $client = new Client($this->getConfig());
    $this->options['ContentId'] = $content_id;
    $this->options['PropertyId'] = $property_id;
    $this->options['debug'] = TRUE;

    try {
      $request = $client
        ->get($this->atmHost . '/atm-admin/content/retrieve', $this->options);
      $response = Json::decode($request->getBody()->getContents());

      return $response;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

  /**
   * Put content to ATM api to process it.
   *
   * @param string $content_id
   *   Content identifier.
   * @param $property_id
   *   Property identifier.
   * @param string $text
   *   Text that should be processed.
   *
   * @return mixed|null
   *   Request content response or FALSE on error.
   */
  public function createLockedContent($content_id, $property_id, $text) {
    $client = new Client($this->getConfig());

    try {
      $request = $client->put($this->atmHost . '/atm-admin/content/create', [
        'headers' => $this->getConfig('headers'),
        'json' => [
          'ContentId' => $content_id,
          'PropertyId' => $property_id,
          'Content' => $text,
        ],
      ]);

      if ($request->getStatusCode() == 200) {
        return TRUE;
      }
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

}
