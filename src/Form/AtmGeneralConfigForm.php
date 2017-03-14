<?php

namespace Drupal\atm\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use GuzzleHttp\Exception\ClientException;

/**
 * Provides form for save basic configs, register new customer and genearte atm.js file.
 */
class AtmGeneralConfigForm extends AtmAbstractForm {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'atm-general-config';
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
      '#description' => $this->t('Choose the country of origin where revenue will be collected'),
      '#required' => TRUE,
      '#ajax' => [
        'event' => 'change',
        'callback' => [$this, 'selectCountryCallback'],
      ],
    ];

    $form['revenue_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Revenue model'),
      '#options' => [],
      '#default_value' => $this->getHelper()->get('revenue_method'),
      '#description' => $this->t('Choose the revenue model that will be used on this blog'),
    ];

    foreach ($this->getHelper()->getRevenueModelList() as $value => $name) {
      $form['revenue_method']['#options'][$value] = $name;
    }

    $form['save'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
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
    $response = new AjaxResponse();

    $this->getHelper()->setApiCountry($form_state->getValue('country'));
    $this->getHelper()->set('revenue_method', $form_state->getValue('revenue_method'));

    $this->getAtmHttpClient()->propertyCreate();

    $errors = drupal_get_messages('error');
    if ($errors) {
      $errors = $errors['error'];
    }

    $errors = array_merge($form_state->getErrors(), $errors);

    if ($errors) {
      $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
      $response->setAttachments($form['#attached']);

      $_errors = [];
      foreach ($errors as $error) {
        if (!$error instanceof TranslatableMarkup) {
          $error = $this->t($error);
        }

        $_errors[] = $this->getErrorMessage($error);
      }

      $response->addCommand(
        new OpenModalDialogCommand('Form errors', $_errors)
      );

      $form_state->clearErrors();
    }

    return $response;
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
