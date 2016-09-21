<?php

namespace Drupal\adtechmedia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AdTechMediaConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adtechmedia_settings';
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

    $form['atm_api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('ATM API key'),
      '#default_value' => $config->get('atm_api_key'),
      '#required' => TRUE,
    );

    $form['atm_api_value'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('ATM API value'),
      '#default_value' => $config->get('atm_api_value'),
      '#required' => TRUE,
    );

    $form['country'] = array(
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => array(
        'usa' => $this->t('USA'),
        'md' => $this->t('Moldova')
      ), //@todo from api.
      '#default_value' => $config->get('country'),
    );

    $form['revenue_model'] = array(
      '#type' => 'select',
      '#title' => $this->t('Revenue model'),
      '#options' => array(
        'advertising' => $this->t('Advertising'),
        'micropayments' => $this->t('Micropayments'),
        'advertising_micropayments' => $this->t('Advertising & Micropayments'),
      ),
      '#default_value' => $config->get('revenue_model'),
      '#description' => $this->t('Select revenue model'),
    );

    $form['content_preview'] = array(
      '#type' => 'select',
      '#title' => $this->t('Content preview'),
      '#options' => array(
        '100_words' => $this->t('100 words'),
        '3_paragraphs' => $this->t('3 paragraphs'),
      ),
      '#default_value' => $config->get('content_preview'),
      '#description' => $this->t('Select how content should be displayed.'),
    );

    $form['locking_algorithm'] = array(
      '#type' => 'select',
      '#title' => $this->t('Content locking algorithm'),
      '#options' => array(
        'blur' => $this->t('Blur'),
        'scramble' => $this->t('Scramble'),
        'keywords' => $this->t('Keywords'),
        'blur_scramble' => $this->t('Blur & Scramble'),
      ),
      '#default_value' => $config->get('locking_algorithm'),
      '#description' => $this->t('How locked content should look.'),
    );

    $form['content_pricing'] = array(
      '#type' => 'select',
      '#title' => $this->t('Content pricing'),
      '#options' => array(
        'autopricing' => $this->t('Autopricing'),
        29 => $this->t('29 cents'),
        49 => $this->t('49 cents'),
      ),
      '#default_value' => $config->get('content_pricing'),
      '#description' => $this->t('Select content price.'),
    );

    $form['content_currency'] = array(
      '#type' => 'select',
      '#title' => $this->t('Content currency'),
      '#options' => array(
        'usd' => $this->t('USD'),
        'mdl' => $this->t('MDL'),
      ),
      '#default_value' => $config->get('content_currency'),
      '#description' => $this->t('Select content currency.'),
    );

    $form['content_paywall'] = array(
      '#type' => 'select',
      '#title' => $this->t('Content paywall'),
      '#options' => array(
        '3_transactions' => $this->t('3 transactions'),
        '100_cents' => $this->t('100 cents'),
      ),
      '#default_value' => $config->get('content_paywall'),
      '#description' => $this->t('Remove paywall after.'),
    );

    $form['template_management'] = array(
      '#type' => 'select',
      '#title' => $this->t('Templates management'),
      '#options' => array(
        'pledge_view' => $this->t('Pledge View'),
        'pay_view' => $this->t('Pay View'),
        'login_view' => $this->t('Login View'),
      ),
      '#default_value' => $config->get('template_management'),
      '#description' => $this->t('Template management.'),
    );

    $form['dns_access'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('DNS access'),
      '#default_value' => $config->get('dns_access'),
      '#description' => $this->t('Route 53 AWS key.'),
    );

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
