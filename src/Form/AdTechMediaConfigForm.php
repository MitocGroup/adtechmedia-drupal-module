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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('adtechmedia.settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General configuration'),
      '#open' => TRUE,
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
        'callback' => [$this, 'regenerateApiKeyCallback'],
        'event' => 'click',
        'wrapper' => 'edit-api-key',
        'method' => 'replaceWith',
      ],
      '#limit_validation_errors' => [],
      '#submit' => [],
    ];

    $form['general']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => [
        'usa' => $this->t('USA'),
        'md' => $this->t('Moldova'),
      ],
      '#default_value' => $config->get('country'),
    ];

    $form['general']['revenue_model'] = [
      '#type' => 'select',
      '#title' => $this->t('Revenue Model'),
      '#options' => [
        'advertising' => $this->t('Advertising'),
        'micropayments' => $this->t('Micropayments'),
        'advertising_micropayments' => $this->t('Advertising & Micropayments'),
      ],
      '#default_value' => $config->get('revenue_model'),
      '#description' => $this->t('Select revenue model'),
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
    ];

    $form['content']['content_pricing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content pricing'),
      '#default_value' => $config->get('content_pricing'),
      '#description' => $this->t('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters'),
      '#placeholder' => $this->t('number'),
      '#title_display' => 'after',
    ];

    $form['content']['content_currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Content currency'),
      '#options' => [
        'usd' => $this->t('USD'),
        'mdl' => $this->t('MDL'),
      ],
      '#default_value' => $config->get('content_currency'),
      '#description' => $this->t('Select content currency.'),
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
        'blur_scramble' => $this->t('Blur & Scramble'),
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

    $form['template'] = [
      '#type' => 'details',
      '#title' => $this->t('Templates management'),
      '#open' => TRUE,
    ];

    $form['template']['vertical_tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-tab',
    ];

    $templates = [
      'pledge_view' => [
        'title' => $this->t('Pledge View'),
        'name' => 'pledgeComponent',
      ],
      'ad_view' => [
        'title' => $this->t('Advertising View'),
        'name' => 'adComponent',
      ],
      'pay_view' => [
        'title' => $this->t('Pay View'),
        'name' => 'payComponent',
      ],
      'refund_view' => [
        'title' => $this->t('Refund View'),
        'name' => 'refundComponent',
      ],
      'unlock_view' => [
        'title' => $this->t('Unlock View'),
        'name' => 'pledgeComponent',
      ],
      'price_view' => [
        'title' => $this->t('Price View'),
        'name' => 'payComponent',
      ],
    ];

    foreach ($templates as $name => $template) {
      $form['template']['tab_' . $name] = [
        '#type' => 'details',
        '#title' => $template['title'],
        '#group' => 'vertical_tabs',
      ];

      $default_template = $config->get($name)['value'];
      if (empty($config->get($name)['value'])) {
        $default_template = self::getTemplate($template['name']);
      }

      $form['template']['tab_' . $name][$name] = [
        '#type' => 'text_format',
        '#format' => $config->get($name)['format'],
        '#default_value' => $default_template,
        '#rows' => 14,
      ];
    }

    $form['#attached']['library'][] = 'adtechmedia/adtechmedia.api';

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

    // Create ATM Property.
    $this->atmClient->createAtmProperty();

    parent::submitForm($form, $form_state);
  }

  /**
   * Ajax callback to regenerate new api key.
   */
  public function regenerateApiKeyCallback($form, $form_state) {
    //$request = new AtmClient();
    $request = $this->atmClient;
    $atm_response = $request->regenerateApiKey();

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(
      '#edit-api-key',
      'val',
      [isset($atm_response['Key']) ? $atm_response['Key'] : '']
    ));

    return $response;
  }

  /**
   * Get template code.
   *
   * @param string $name
   *   Template name.
   * @return mixed
   *   Template markup.
   */
  public static function getTemplate($name) {
    $client = new AtmClient();
    $templates = $client->templatesLoad();

    return $templates[$name];
  }

}
