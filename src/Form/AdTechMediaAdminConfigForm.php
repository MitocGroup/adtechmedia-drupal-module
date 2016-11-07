<?php

namespace Drupal\adtechmedia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * AdTechMedia module configuration.
 */
class AdTechMediaAdminConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adtechmedia_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['adtechmedia.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.settings');

    $form['main'] = [
      '#type' => 'details',
      '#title' => $this->t('Main configuration'),
      '#open' => TRUE,
    ];

    $form['main']['use_atm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable AdTechMedia'),
      '#default_value' => $config->get('use_atm'),
      '#description' => $this->t('Activate/Deactivate AdTechMedia service.'),
    ];

    $form['main']['atm_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ATM Host'),
      '#default_value' => $config->get('atm_host'),
      '#description' => $this->t('Configure AdTechMedia service host.'),
      '#required' => TRUE,
    ];

    $form['main']['atm_base_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ATM Base Path'),
      '#default_value' => $config->get('atm_base_path'),
      '#required' => TRUE,
    ];

    $form['main']['atm_templates_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ATM templates path'),
      '#default_value' => $config->get('atm_templates_path'),
      '#required' => TRUE,
    ];

    $form['debug'] = [
      '#type' => 'details',
      '#title' => $this->t('Development'),
    ];

    $form['debug']['development'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable development mode'),
      '#default_value' => $config->get('development'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.settings');

    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
