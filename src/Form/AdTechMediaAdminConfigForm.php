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
    return ['adtechmedia.admin_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.admin_settings');

    $form['main'] = array(
      '#type' => 'details',
      '#title' => $this->t('Main configuration'),
      '#open' => TRUE,
    );

    $form['main']['use_atm'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable AdTechMedia'),
      '#default_value' => $config->get('use_atm'),
      '#description' => $this->t('Activate/Deactivate AdTechMedia service.'),
    );

    $form['debug'] = array(
      '#type' => 'details',
      '#title' => $this->t('Development'),
    );

    $form['debug']['development'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable development mode'),
      '#default_value' => $config->get('development'),
    );

    return parent::buildForm($form, $form_state);
  }

}
