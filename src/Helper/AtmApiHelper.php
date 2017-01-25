<?php

namespace Drupal\atm\Helper;

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
    return $this->getConfig()->get('Name');
  }

  /**
   * Save API Name.
   */
  public function setApiName($name) {
    $this->getConfig()->set('Name', $name)->save();
  }

  /**
   * Get API Email.
   */
  public function getApiEmail() {
    return $this->getConfig()->get('api_email');
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
}