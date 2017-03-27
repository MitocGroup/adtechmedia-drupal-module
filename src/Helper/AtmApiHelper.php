<?php

namespace Drupal\atm\Helper;

use Drupal\atm\AtmException;

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
   * Get service AtmHttpClient.
   *
   * @return \Drupal\atm\AtmHttpClient
   *   Get service AtmHttpClient.
   */
  public function getAtmHttpClient() {
    return \Drupal::service('atm.http_client');
  }

  /**
   * Generate atm api key.
   */
  public function generateApiKey() {
    $name = \Drupal::config('system.site')->get('name');
    $apiKey = $this->getAtmHttpClient()->generateApiKey($name, TRUE);

    $this->setApiKey($apiKey);
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
   *
   * @throws \Drupal\atm\AtmException
   */
  public function saveBuildPath($remote, $local, $scheme = NULL) {
    $scheme = is_null($scheme) ? file_default_scheme() : $scheme;
    $path_schema = $scheme . $local;

    /** @var \Drupal\Core\File\FileSystem $file_system */
    $file_system = \Drupal::service('file_system');

    $dirname = dirname($path_schema);
    if (!is_dir($dirname)) {
      $created = $file_system->mkdir($dirname, 0755, TRUE);
      if (!$created) {
        throw new AtmException(
          "Directory `atm` wasn't created. Check permissions on public files directory. This directory must exist and be writable by Drupal"
        );
      }
    }

    $realpath = $file_system->realpath($path_schema);

    $script = file_get_contents($remote . '?' . microtime());
    $script = gzdecode($script);

    if (file_exists($realpath)) {
      unlink($realpath);
    }

    file_put_contents($realpath, $script);

    return file_create_url($path_schema);
  }

  /**
   * Return default theme config.
   *
   * @param bool $editable
   *   Return editable or not config.
   *
   * @return \Drupal\Core\Config\Config
   *   Return default theme config.
   */
  public function getThemeConfig($editable = FALSE) {
    /** @var \Drupal\Core\Extension\ThemeHandler $themeHandler */
    /** @var \Drupal\Core\Config\Config $themeConfig */

    $themeHandler = \Drupal::service('theme_handler');
    $defaultTheme = $themeHandler->getTheme($themeHandler->getDefault());

    $themeName = $defaultTheme->getName();

    if ($editable) {
      $themeConfig = \Drupal::configFactory()->clearStaticCache()->getEditable("atm.styles.target-cb.{$themeName}");
    }
    else {
      $themeConfig = \Drupal::configFactory()->clearStaticCache()->get("atm.styles.target-cb.{$themeName}");
    }

    return $themeConfig;
  }

  /**
   * Get css.
   *
   * @return string
   *   Css.
   */
  public function getTemplateOwerallStyles() {
    $themeConfig = $this->getThemeConfig();

    $bg  = $themeConfig->get('background-color') !== NULL ? $themeConfig->get('background-color') : $this->get('styles.target-cb.background-color');
    $br  = $themeConfig->get('border') !== NULL ? $themeConfig->get('border') : $this->get('styles.target-cb.border');
    $fbg = $themeConfig->get('footer-background-color') !== NULL ? $themeConfig->get('footer-background-color') : $this->get('styles.target-cb.footer-background-color');
    $fb  = $themeConfig->get('footer-border') !== NULL ? $themeConfig->get('footer-border') : $this->get('styles.target-cb.footer-border');
    $ff  = $themeConfig->get('font-family') !== NULL ? $themeConfig->get('font-family') : $this->get('styles.target-cb.font-family');
    $bs  = $themeConfig->get('box-shadow') != NULL ? $themeConfig->get('box-shadow') : $this->get('styles.target-cb.box-shadow');

    return str_replace([
      '{{background-color}}',
      '{{border}}',
      '{{box-shadow}}',
      '{{footer-background-color}}',
      '{{footer-border}}',
      '{{font-family}}',
    ], [
      $bg,
      $br,
      $bs,
      $fbg,
      $fb,
      $ff,
    ], $this->getCssTemplate());
  }

  /**
   * Get CSS template for atm-modal.
   */
  public function getCssTemplate() {
    return "
    .atm-base-modal {background-color: {{background-color}};}
    .atm-targeted-modal .atm-head-modal .atm-modal-heading {background-color: {{background-color}};}
    .atm-targeted-modal{border: {{border}};}
    .atm-targeted-modal{box-shadow: {{box-shadow}};}
    .atm-base-modal .atm-footer {background-color: {{footer-background-color}};border: {{footer-border}};}
    .atm-targeted-container .mood-block-info,
    .atm-targeted-modal,
    .atm-targeted-modal .atm-head-modal .atm-modal-body p,
    .atm-unlock-line .unlock-btn {font-family: {{font-family}};}
    .atm-button {line-height: normal;}";
  }

  /**
   * Generate atm.js.
   */
  public function propertyCreate() {
    if (!$this->get('property_id')) {
      $this->getAtmHttpClient()->propertyCreate();
    }
  }

  /**
   * Create theme config.
   */
  public function createThemeConfig() {
    if (!$this->getThemeConfig()->get('theme-config-id') || !$this->get('property_id')) {
      $this->getAtmHttpClient()->createThemeConfig();
    }
  }

  /**
   * Get selected Content Types.
   *
   * @return array
   *   Content Types.
   */
  public function getSelectedContentTypes() {
    return $this->get('selected-ct') !== NULL ? $this->get('selected-ct') : [];
  }

}
