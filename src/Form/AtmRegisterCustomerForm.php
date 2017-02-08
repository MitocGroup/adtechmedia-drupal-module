<?php

namespace Drupal\atm\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\locale\StringBase;
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
        $countries = $this->getHelper()->getSupportedCountries();

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
      '#options' => $options,
      '#default_value' => $this->getHelper()->getApiCountry(),
      '#required' => TRUE,
      '#ajax' => [
        'event' => 'change',
        'callback' => [$this, 'selectCountryCallback'],
      ],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => \Drupal::config('system.site')->get('mail'),
      '#required' => TRUE,
    ];

    $form['save'] = [
      '#type' => 'button',
      '#value' => t('Register'),
      '#ajax' => [
        'event' => 'click',
        'callback' => [$this, 'saveParams'],
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

  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function saveParams(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $email = $values['email'];
    $country = $values['country'];

    $this->getHelper()->setApiEmail($email);
    $this->getHelper()->setApiCountry($country);

    /** @var \Drupal\atm\AtmHttpClient $httpClient */
    $httpClient = \Drupal::service('atm.http_client');
    $httpClient->propertyCreate();

    return new AjaxResponse();
  }

  /**
   * Callback for country select.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   ajax response.
   */
  public function selectCountryCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $country = $form_state->getValue('country');
    if (empty($country)) {
      $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
      $response->setAttachments($form['#attached']);

      $response->addCommand(
        new OpenModalDialogCommand(
          '', $this->getErrorMessage($this->t('Please, select country'))
        )
      );
      return $response;
    }

    $this->getHelper()->setApiCountry($country);

    $response->addCommand(
      new InvokeCommand('#edit-price-currency', 'setOptions', [$this->getHelper()->getCurrencyList()])
    );

    $response->addCommand(
      new InvokeCommand('#edit-revenue-method', 'setOptions', [$this->getHelper()->getRevenueModelList()])
    );

    return $response;
  }

}
