<?php

namespace Drupal\adtechmedia\Form;

use Drupal\adtechmedia\AtmClient;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AdTechMedia Service configuration.
 */
class AdTechMediaConfigForm extends ConfigFormBase {

  /**
   * ATM Guzzle client.
   *
   * @var \Drupal\adtechmedia\AtmClient
   */
  private $atmClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, AtmClient $atm_client) {
    parent::__construct($config_factory);

    $this->atmClient = $atm_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('atm.client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adtechmedia_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['adtechmedia.settings'];
  }

  /**
   * Get a list of all configuration options.
   *
   * @return array
   *   A list of configuration options.
   */
  private function getConfigurationOptions() {
    return [
      'api_key',
      'country',
      'revenue_model',
      'email',
      'content_pricing',
      'content_currency',
      'content_paywall',
      'content_paywall_type',
      'content_preview',
      'content_preview_type',
      'locking_algorithm',
      'dns_access',
      'social_media',
      'pledge_view',
      'ad_view',
      'pay_view',
      'refund_view',
      'price_view',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.settings');

    $methods = [];
    $currencies = [];
    if (!empty($config->get('api_key')) && !empty($config->get('property_id'))) {
      $countries = $this->countryInfo();
      $name = $config->get('country');
      list($methods, $currencies) = $this->getCountryOptions($name);
    }

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General configuration'),
      '#open' => TRUE,
      '#attributes' => [
        'class' => ['general-settings'],
      ],
    ];

    $form['general']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
      '#title_display' => 'after',
      '#suffix' => '<span class="bar"></span>',
    ];

    $form['general']['regenerate'] = [
      '#type' => 'button',
      '#value' => $this->t('Regenerate'),
      '#attributes' => [
        'class' => ['btn', 'regenerate'],
      ],
      '#ajax' => [
        'callback' => '::regenerateApiKeyCallback',
        'event' => 'click',
        'wrapper' => 'edit-api-key',
        'method' => 'replaceWith',
      ],
      '#limit_validation_errors' => [],
    ];

    $form['general']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => isset($countries['countries']) ? ['' => '- Select -'] + $countries['countries'] : [$this->t('- Select -')],
      '#default_value' => $config->get('country'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateCountryInfo',
        'wrapper' => 'country-data',
        'method' => 'replace',
      ],
    ];

    $form['general']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#default_value' => $config->get('email'),
      '#title_display' => 'after',
    ];

    $form['general']['activate'] = [
      '#type' => 'button',
      '#value' => $this->t('Activate'),
      '#attributes' => [
        'class' => ['btn', 'activate'],
      ],
    ];

    $form['content'] = [
      '#type' => 'details',
      '#title' => $this->t('Content configuration'),
      '#open' => TRUE,
      '#attributes' => [
        'class' => ['content-settings'],
      ],
    ];

    $form['content']['country_data'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'country-data'],
    ];

    $form['content']['country_data']['revenue_model'] = [
      '#type' => 'select',
      '#title' => $this->t('Revenue Model'),
      '#options' => $methods,
      '#default_value' => $config->get('revenue_model'),
      '#description' => $this->t('Select revenue model'),
      '#description_display' => TRUE,
      '#validated' => TRUE,
    ];

    $form['content']['country_data']['content_currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Content currency'),
      '#options' => $currencies,
      '#default_value' => $config->get('content_currency'),
      '#description' => $this->t('Select content currency.'),
      '#description_display' => TRUE,
      '#validated' => TRUE,
    ];

    $form['content']['content_pricing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content pricing'),
      '#default_value' => $config->get('content_pricing'),
      '#description' => $this->t('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters'),
      '#placeholder' => $this->t('number'),
      '#title_display' => 'after',
    ];

    $form['content']['content_paywall'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content paywall'),
      '#default_value' => $config->get('content_paywall'),
      '#description' => $this->t('Remove paywall after.'),
      '#placeholder' => $this->t('number'),
      '#title_display' => 'after',
    ];

    $form['content']['content_paywall_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Content paywall type'),
      '#options' => [
        'transactions' => $this->t('Transactions'),
      ],
      '#default_value' => $config->get('content_paywall_type'),
      '#description' => $this->t('Select preview type.'),
    ];

    $form['content']['content_preview'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content preview'),
      '#default_value' => $config->get('content_preview'),
      '#description' => $this->t('Select how content should be displayed.'),
      '#placeholder' => $this->t('number'),
      '#title_display' => 'after',
    ];

    $form['content']['content_preview_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Content preview type'),
      '#options' => [
        'elements' => $this->t('Elements'),
        'words' => $this->t('Words'),
      ],
      '#default_value' => $config->get('content_preview_type'),
      '#description' => $this->t('Select preview type.'),
    ];

    $form['content']['locking_algorithm'] = [
      '#type' => 'select',
      '#title' => $this->t('Content unlocking algorithm'),
      '#options' => [
        'blur' => $this->t('Blur'),
        'scramble' => $this->t('Scramble'),
        'keywords' => $this->t('Keywords'),
        'blur+scramble' => $this->t('Blur & Scramble'),
      ],
      '#default_value' => $config->get('locking_algorithm'),
      '#description' => $this->t('How locked content should look.'),
    ];

    $form['content']['dns_access'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DNS access'),
      '#default_value' => $config->get('dns_access'),
      '#description' => $this->t('Route 53 AWS key.'),
      '#title_display' => 'after',
    ];

    $form['content']['social_media'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Social media credentials'),
      '#default_value' => $config->get('social_media'),
      '#description' => $this->t('Select social media.'),
      '#title_display' => 'after',
    ];

    $form['#attached']['library'][] = 'adtechmedia/adtechmedia.admin';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.settings');

    $atm_config = [];
    foreach ($form_state->getValues() as $key => $value) {
      if (in_array($key, $this->getConfigurationOptions())) {
        $atm_config[$key] = $value;
        $config->set($key, $value)->save();
      }
    }

    // Encode ATM modal css.
    //$styles = file_get_contents(drupal_get_path('module', 'adtechmedia') . '/css/atm-modal.css');
    //$atm_config['styles'] = base64_encode($styles);
    //$atm_config['styles'] = base64_encode('body {background: red;}');

    // Update ATM Property settings.
    $client = new AtmClient();
    $client->updateAtmProperty($atm_config);

    parent::submitForm($form, $form_state);
  }

  /**
   * Ajax callback to regenerate new api key.
   */
  public function regenerateApiKeyCallback($form, $form_state) {
    $config = $this->config('adtechmedia.settings');

    $client = new AtmClient();
    $atm_key = $client->regenerateApiKey();

    if (isset($atm_key['Key'])) {
      $config->set('api_key', $atm_key['Key'])->save();
    }

    // Create ATM Property.
    $property = $client->createAtmProperty();

    if (isset($property['Id'])) {
      $config->set('property_id', $property['Id'])->save();
    }

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(
      '#edit-api-key',
      'val',
      [isset($atm_key['Key']) ? $atm_key['Key'] : '']
    ));

    return $response;
  }

  /**
   * Get information about available countries.
   *
   * @return array
   *   An array of country data.
   */
  public function countryInfo() {
    $client = new AtmClient();
    $countries = $client->getSupportedCountries();

    if (empty($countries['Countries'])) {
      return [];
    }

    $info = [];
    foreach ($countries['Countries'] as $country) {
      $info['countries'][$country['UN']] = $country['Name'];
      $info[$country['UN']] = [
        'revenue_model' => array_combine($country['RevenueModel'], $country['RevenueModel']),
        'content_currency' => array_combine($country['Currency'], $country['Currency']),
      ];
    }

    return $info;
  }

  /**
   * Get a list of populated options with country information.
   *
   * @param string $name
   *    Country name.
   *
   * @return array
   *   An array for revenue methods and available currencies.
   */
  public function getCountryOptions($name, $wrapped = FALSE) {
    $methods = [];
    $currencies = [];

    if (!empty($name)) {
      // Get countries information.
      $countries = $this->countryInfo();
      $methods = array_merge($methods, $countries[$name]['revenue_model']);
      $currencies = array_merge($currencies, $countries[$name]['content_currency']);
    }

    return [$methods, $currencies];
  }

  /**
   * Ajax callback to update fields which data depends of selected country.
   */
  public function updateCountryInfo(array &$form, FormStateInterface $form_state) {
    // Get selected country name.
    $name = $form_state->getValue('country');

    // Save country to use later in form options.
    $config = $this->config('adtechmedia.settings');
    $config->set('country', $name)->save();

    // Get country options.
    list($methods, $currencies) = $this->getCountryOptions($name);

    $form['content']['country_data']['revenue_model'] = [
      '#type' => 'select',
      '#title' => $this->t('Revenue Model'),
      '#options' => $methods,
      '#default_value' => $config->get('revenue_model'),
      '#description' => $this->t('Select revenue model'),
      '#description_display' => TRUE,
      '#validated' => TRUE,
    ];

    $form['content']['country_data']['content_currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Content currency'),
      '#options' => $currencies,
      '#default_value' => $config->get('content_currency'),
      '#description' => $this->t('Select content currency.'),
      '#description_display' => TRUE,
      '#validated' => TRUE,
    ];

    return $form['content']['country_data'];
  }

}
