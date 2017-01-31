<?php
namespace Drupal\atm\Form;

use Drupal\atm\Helper\AtmApiHelper;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AtmConfigForm extends FormBase {

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
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'atm-admin-general-configuration-form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];

    if ($this->getHelper()->getApiKey()) {
      /** @var \Drupal\atm\AtmHttpClient $httpClient */
      $httpClient = \Drupal::service('atm.http_client');
      $countries = $httpClient->getPropertySupportedCountries();

      foreach ($countries as $country) {
        $options[$country['ISO']] = t($country['Name']);
      }
    }

    $form['country'] = [
      '#type' => 'select',
      '#title' => t('Country'),
      '#empty_option' => t('- Select -'),
      '#options' => $options,
      '#default_value' => $this->getHelper()->getApiCountry(),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => $this->getHelper()->getApiEmail(),
      '#required' => TRUE,
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#ajax' => [
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $name = $this->getHelper()->getApiName();
    $website = $_SERVER['HTTP_HOST'];
    $email = $values['email'];
    $country = $values['country'];

    /** @var \Drupal\atm\AtmHttpClient $httpClient */
    $httpClient = \Drupal::service('atm.http_client');

    $response = $httpClient->propertyCreate($name, $website, $email, $country);

    $path_schema = file_default_scheme() . "://atm";

    /** @var \Drupal\Core\File\FileSystem $file_system */
    $file_system = \Drupal::service('file_system');

    $path = \Drupal::service('file_system')->realpath($path_schema);

    if (!is_dir($path)) {
      $file_system->mkdir($path, 0777, TRUE);
    }

    $script = file_get_contents($response['BuildPath']);
    $script = gzdecode($script);

    file_put_contents($path . '/atm.min.js', $script);

    $url = file_create_url($path_schema . '/atm.min.js');

    $this->getHelper()->set('build_path', $url);
    $this->getHelper()->setApiEmail($email);
    $this->getHelper()->setApiCountry($country);
  }
}