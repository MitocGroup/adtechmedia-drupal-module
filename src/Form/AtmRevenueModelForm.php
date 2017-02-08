<?php

namespace Drupal\atm\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AtmRevenueModelForm.
 */
class AtmRevenueModelForm extends AtmAbstractForm {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'atm-admin-revenue-model-form';
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

    $description = [];
    $description[] = $this->t('<strong>IMPORTANT:</strong>');
    $description[] = $this->t('Registration step is not required to be able to use this plugin.');
    $description[] = $this->t('Once you generate some revenue and want to transfer it into your bank account, then we encourage you to register here (using "Email address").');
    $description[] = $this->t('Follow the steps to setup your account on AdTechMedia.io platform and enjoy the influx of revenue into your bank account.');

    $form['info'] = [
      '#type' => 'markup',
      '#markup' => implode(" ", $description),
    ];

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
    $this->getHelper()->set('revenue_method', $form_state->getValue('revenue_method'));
    return new AjaxResponse();
  }

}
