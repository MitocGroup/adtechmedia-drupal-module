<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Provides form for save basic configs, register new customer and genearte atm.js file.
 */
class AtmRegisterCustomerForm extends AtmAbstractForm {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'atm-admin-register-customer-form';
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

      try {
        /** @var \Drupal\atm\AtmHttpClient $httpClient */
        $httpClient = \Drupal::service('atm.http_client');
        $countries = $httpClient->getPropertySupportedCountries();

        foreach ($countries as $country) {
          $options[$country['ISO']] = $country['Name'];
        }
      }
      catch (ClientException $exception) {
        drupal_set_message(
          $exception->getMessage(), 'error'
        );
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
      '#default_value' => \Drupal::config('system.site')->get('mail'),
      '#required' => TRUE,
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#value' => t('Register'),
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

    $email = $values['email'];
    $country = $values['country'];

    $this->getHelper()->setApiEmail($email);
    $this->getHelper()->setApiCountry($country);

    /** @var \Drupal\atm\AtmHttpClient $httpClient */
    $httpClient = \Drupal::service('atm.http_client');
    $httpClient->propertyCreate();
  }

}
