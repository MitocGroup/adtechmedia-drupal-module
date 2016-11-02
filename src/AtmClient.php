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
   * AdTechMedia host.
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
   * ATM property id.
   *
   * @var string
   */
  private $propertyId;

  /**
   * ATM configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $atmConfig;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $config = []) {
    $this->atmConfig = \Drupal::config('adtechmedia.settings');

    $this->propertyId = $this->atmConfig->get('property_id');
    $this->atmHost = $this->atmConfig->get('atm_host') . '/' . $this->atmConfig->get('atm_base_path');

    $this->setHeader('X-Api-Key', $this->atmConfig->get('api_key'));
    $this->setHeader('Content-Type', 'application/json');

    $config['base_uri'] = $this->atmHost;
    $config['headers']['X-Api-Key'] = $this->atmConfig->get('api_key');
    $config['headers']['Content-Type'] = 'application/json';

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
   * Set request query.
   *
   * @param string $name
   *   Header property name.
   * @param string $value
   *   Header property value.
   */
  private function setQuery($name, $value) {
    $this->options['query'][$name] = $value;
  }

  /**
   * PUT request to regenerate ATM API key.
   *
   * @return mixed|false
   *   Request content response or FALSE on error.
   */
  public function regenerateApiKey() {
    $client = new Client($this->getConfig());

    try {
      // Generate ATM API key.
      $request = $client->put($this->atmHost . '/atm-admin/api-gateway-key/create', [
        'json' => [
          'Name' => 'Drupal:' . \Drupal::config('system.site')->get('name'),
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
   *
   * @return mixed|false
   *   Request content response or FALSE on error.
   */
  public function retrieveLockedContent($content_id) {
    $client = new Client($this->getConfig());
    $this->setQuery('ContentId', $content_id);
    $this->setQuery('PropertyId', $this->propertyId);

    // Get ATM config.
    $config = \Drupal::configFactory()->get('adtechmedia.settings');
    $this->setQuery('ScrambleStrategy', $config->get('locking_algorithm'));

    if (!empty($config->get('content_preview_type'))) {
      $this->setQuery('OffsetType', $config->get('content_preview_type'));
    }

    if (!empty($config->get('content_preview'))) {
      $this->setQuery('Offset', $config->get('content_preview'));
    }

    try {
      $request = $client->get(
        $this->atmHost . '/atm-admin/content/retrieve',
        $this->options
      );

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
   * @param string $text
   *   Text that should be processed.
   *
   * @return mixed|false
   *   Request content response or FALSE on error.
   */
  public function createLockedContent($content_id, $text) {
    $client = new Client($this->getConfig());

    try {
      $request = $client->put($this->atmHost . '/atm-admin/content/create', [
        'headers' => $this->getConfig('headers'),
        'json' => [
          'ContentId' => $content_id,
          'PropertyId' => $this->propertyId,
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

  /**
   * Create new ATM Property.
   *
   * @return array|bool
   *   An array of property config options or false on error.
   */
  public function createAtmProperty() {
    $client = new Client($this->getConfig());

    try {
      $request = $client->put($this->atmHost . '/atm-admin/property/create', [
        'headers' => $this->getConfig('headers'),
        'json' => [
          'Name' => 'Drupal:' . \Drupal::config('system.site')->get('name'),
          'Website' => \Drupal::request()->getHost(),
        ],
      ]);

      $response = Json::decode($request->getBody()->getContents());

      if ($this->atmConfig->get('development')) {
        \Drupal::logger('adtechmedia')
          ->debug('ATM Property ID: @property', [
            '@property' => $response['Id'],
          ]);
      }

      return $response;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

  /**
   * Retrieve ATM Property.
   *
   * @return array|bool
   *   An array of property config options or false on error.
   */
  public function retrieveAtmProperty() {
    $client = new Client($this->getConfig());
    $this->setQuery('Id', $this->propertyId);

    try {
      $request = $client
        ->get($this->atmHost . '/atm-admin/property/retrieve', $this->options);
      $response = Json::decode($request->getBody()->getContents());

      return $response;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

  /**
   * Update ATM Property.
   *
   * @param array $config
   *   ATM properties.
   *
   * @return bool|mixed
   *   Updated property.
   */
  public function updateAtmProperty($config) {
    $client = new Client($this->getConfig());

    try {
      $request = $client->patch($this->atmHost . '/atm-admin/property/update-config', [
        'headers' => $this->getConfig('headers'),
        'json' => [
          'Id' => $this->propertyId,
          'ConfigDefaults' => [
            'revenueMethod' => $config['revenue_model'],
            'payment' => [
              'pledge' => $config['content_paywall'],
              'price' => $config['content_pricing'],
              'currency' => $config['content_currency'],
            ],
            'content' => [
              'offsetType' => $config['content_preview_type'],
              'offset' => $config['content_preview'],
              'lock' => $config['locking_algorithm'],
              'container' => 'div[property="schema:text"]',
              'selector' => 'p,h1,h2,h3,h4,h5,h6,ol,ul',
              'authorCb' => "function (onReady) { var metaNode = document.querySelector('.node__meta span[property=\"schema:name\"]'); onReady({ fullName: metaNode.textContent, avatar: 'https://avatars.io/twitter/nytimes' }) }",
            ],
            'styles' => ['main' => $config['styles']],
            'ads' => [
              'relatedVideoCb' => "function(onReady) { onReady('https://www.youtube.com/watch?v=OzQh6wDb2oE&t=23s') }",
            ],
            'targetModal' => [
              'toggleCb' => "function (cb) { cb(true) }",
              'targetCb' => "function (mainModal, cb) { mainModal.mount(document.querySelector('.header'), mainModal.constructor.MOUNT_AFTER);cb() }",
            ],
          ],
        ],
      ]);

      $response = Json::decode($request->getBody()->getContents());

      return $response;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

  /**
   * Load an ATM template.
   *
   * @param string|null $name
   *   Load template by name or null to load all templates.
   *
   * @return string|array|bool
   *   Template markup, an array of all templates markup or FALSE on fail.
   */
  public function templatesLoad($name = NULL) {
    $client = new Client();

    try {
      $request = $client->get('https://demo.adtechmedia.io/atm-admin/atm-build/templates.json');
      $response = Json::decode($request->getBody()->getContents());

      if (!empty($name)) {
        return base64_decode($response[$name]);
      }

      // Return all templates.
      $templates = [];
      foreach ($response as $key => $value) {
        $templates[$key] = base64_decode($value);
      }

      return $templates;
    }
    catch (RequestException $e) {
      watchdog_exception('adtechmedia', $e->getMessage());
    }

    return FALSE;
  }

}
