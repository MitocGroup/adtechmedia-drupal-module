<?php

namespace Drupal\atm\Helper;

/**
 * Provides helper for ATM.
 */
class AtmApiHelper {

  /**
   * Get config.
   */
  private function getConfig() {
    return \Drupal::configFactory()->getEditable('atm.settings');
  }

  /**
   * Get API key.
   */
  public function getApiKey() {
    return $this->getConfig()->get('api_key');
  }

  /**
   * Save API key.
   */
  public function setApiKey($key) {
    $this->getConfig()->set('api_key', $key)->save();
  }

  /**
   * Get API Name.
   */
  public function getApiName() {
    return $this->getConfig()->get('name');
  }

  /**
   * Save API Name.
   */
  public function setApiName($name) {
    $this->getConfig()->set('name', $name)->save();
  }

  /**
   * Get API Email.
   */
  public function getApiEmail() {
    $apiEmail = $this->getConfig()->get('api_email');

    if (empty($apiEmail)) {
      $apiEmail = \Drupal::config('system.site')->get('mail');
    }

    return $apiEmail;
  }

  /**
   * Save API Email.
   */
  public function setApiEmail($email) {
    $this->getConfig()->set('api_email', $email)->save();
  }

  /**
   * Get current selected country.
   */
  public function getApiCountry() {
    return $this->getConfig()->get('country');
  }

  /**
   * Save country for API.
   */
  public function setApiCountry($country) {
    $this->getConfig()->set('country', $country)->save();
  }

  /**
   * Provides setter for config.
   */
  public function set($key, $value) {
    $this->getConfig()->set($key, $value)->save();
  }

  /**
   * Provides getter for config.
   */
  public function get($key) {
    return $this->getConfig()->get($key);
  }

  /**
   * Genearete atm api key.
   */
  public function generateApiKey() {
    /** @var \Drupal\atm\AtmHttpClient $http_client */
    $http_client = \Drupal::service('atm.http_client');

    $name = \Drupal::config('system.site')->get('name');

    $api_key = $http_client->generateApiKey($name, TRUE);

    $this->setApiKey($api_key);
    $this->setApiName($name);
  }

  /**
   * Get supported countries.
   *
   * @return array
   *   List of countries.
   */
  public function getSupportedCountries() {
    $cache = \Drupal::cache()->get(__FUNCTION__);
    if ($cache) {
      return $cache->data;
    }

    /** @var \Drupal\atm\AtmHttpClient $httpClient */
    $httpClient = \Drupal::service('atm.http_client');

    $countries = $httpClient->getPropertySupportedCountries();
    if ($countries) {
      \Drupal::cache()->set(__FUNCTION__, $countries);
    }

    return $countries;
  }

  /**
   * Get list of currencies.
   *
   * @return array
   *   Array of currencies.
   */
  public function getCurrencyList() {
    $currencies = [];
    $countries = $this->getSupportedCountries();

    foreach ($countries as $country) {
      if ($country['ISO'] == $this->getApiCountry()) {
        $currencies = array_combine($country['Currency'], array_map('mb_strtoupper', $country['Currency']));
      }
    }

    return $currencies;
  }

  /**
   * Get revenue model list.
   *
   * @return array
   *   Array of revenue model list.
   */
  public function getRevenueModelList() {
    $revenueModels = [];
    $countries = $this->getSupportedCountries();

    foreach ($countries as $country) {
      if ($country['ISO'] == $this->getApiCountry()) {
        $revenueModels = array_combine($country['RevenueModel'], $country['RevenueModel']);
      }
    }

    return $revenueModels;
  }

  /**
   * Save remote file to local fs.
   *
   * @param string $remote
   *   Remote file url.
   * @param string $local
   *   Local destination for save.
   * @param null|string $scheme
   *   File scheme.
   *
   * @return string
   *   Return web-accessible URL of saved file.
   */
  public function saveBuildPath($remote, $local, $scheme = NULL) {
    $scheme = is_null($scheme) ? file_default_scheme() : $scheme;
    $path_schema = $scheme . $local;

    /** @var \Drupal\Core\File\FileSystem $file_system */
    $file_system = \Drupal::service('file_system');

    $realpath = \Drupal::service('file_system')->realpath($path_schema);
    $dirname = dirname($realpath);

    if (!is_dir($dirname)) {
      $file_system->mkdir($dirname, 0644, TRUE);
    }

    $script = file_get_contents($remote);
    $script = gzdecode($script);

    file_put_contents($realpath, $script);

    return file_create_url($path_schema);
  }

  /**
   * Get css.
   *
   * @return string
   *   Css.
   */
  public function getTemplateOwerallStyles() {
    $bg = $this->get('styles.target-cb.background-color');
    $fbg = $this->get('styles.target-cb.footer-background-color');
    $fb = $this->get('styles.target-cb.footer-border');
    $ff = $this->get('styles.target-cb.font-family');
    $bs = $this->get('styles.target-cb.box-shadow');

    return <<<CSS

    .atm-base-modal {
      background-color: #ffffff;
    }
    
    .atm-targeted-modal .atm-head-modal .atm-modal-heading {
      background-color: $bg;
    }
    
    .atm-targeted-modal{
      border: 1px solid #d3d3d3;
    }
    
    .atm-targeted-modal{
      box-shadow: $bs;
    }
    
    .atm-base-modal .atm-footer {
      background-color: $fbg;
      border: $fb;
    }
    
    .atm-targeted-container .mood-block-info,
    .atm-targeted-modal,
    .atm-targeted-modal .atm-head-modal .atm-modal-body p,
    .atm-unlock-line .unlock-btn {
      font-family: $ff;
    }
    
    .atm-button {
      line-height: normal;
    }
CSS;
  }

}
