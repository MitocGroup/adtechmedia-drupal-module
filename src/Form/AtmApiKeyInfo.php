<?php
namespace Drupal\atm\Form;

use Drupal\atm\AtmHttpClient;
use Drupal\atm\Helper\AtmApiHelper;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

class AtmApiKeyInfo extends FormBase {

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
    return 'atm-admin-api-key-info';
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

    $form['api_name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => $this->getHelper()->getApiName(),
      '#required' => TRUE,
    ];

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#default_value' => $this->getHelper()->getApiKey(),
    ];

    $form['api_key_generate'] = [
      '#type' => 'button',
      '#value' => t('Generate'),
      '#ajax' => array(
        'callback' => array($this, 'generateApiKeyCallback'),
        'event' => 'click',
        'wrapper' => 'edit-api-key',
        'method' => 'replaceWith',
      ),
    ];

    $form['api_key_delete'] = [
      '#type' => 'button',
      '#value' => t('Delete'),
      "#disabled" => "disabled",
    ];

    $form['api_key_update'] = [
      '#type' => 'button',
      '#value' => t('Update'),
      "#disabled" => "disabled",
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
    // TODO: Implement submitForm() method.
  }

  /**
   * Callback for generate and save API Key in config.
   */
  public function generateApiKeyCallback($form, FormState $form_state) {
    /** @var AtmHttpClient $http_client */
    $http_client = \Drupal::service('atm.http_client');


    $name = $form_state->getValue('api_name');
    if ($errors = $form_state->getErrors()) {
      //todo: Show error if name is empty
    }

    $api_key = $http_client->generateApiKey($name, TRUE);

    $this->getHelper()->setApiKey($api_key);
    $this->getHelper()->setApiName($name);

    $response = new AjaxResponse();

    $response->addCommand(
      new InvokeCommand('#edit-api-key', 'val', [$api_key])
    );

    return $response;
  }
}