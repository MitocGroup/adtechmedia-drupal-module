<?php

namespace Drupal\adtechmedia\Form;

use Drupal\adtechmedia\AtmClient;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AdTechMedia template management form.
 */
class AdTechMediaTemplateForm extends ConfigFormBase {

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
    return 'adtechmedia_template_management_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['adtechmedia.template_settings'];
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
    //$form = parent::buildForm($form, $form_state);
    $config = $this->config('adtechmedia.template_settings');

    $form['template'] = [
      '#type' => 'details',
      '#title' => $this->t('Templates management'),
      '#open' => TRUE,
      '#attributes' => [
        'class' => ['template-settings'],
      ],
    ];

    $form['template']['overall'] = [
      '#type' => 'details',
      '#title' => $this->t('Overall position and styling'),
      '#open' => TRUE,
      '#attributes' => [
        'data-template' => 'overall-styling',
      ],
    ];

    $form['template']['overall']['top'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container'],
      ],
    ];

    $form['template']['overall']['top']['sticky'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sticky'),
      '#default_value' => $config->get('sticky'),
      '#attributes' => [
        //'id' => 'checkbox-sticky',
        //'data-template' => 'position',
      ],
    ];

    $form['template']['overall']['top']['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#placeholder' => $this->t('width'),
      '#default_value' => $config->get('width'),
      '#attributes' => [
        'data-template' => 'position'
      ],
    ];

    $form['template']['overall']['top']['offset_top'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Offset top'),
      '#placeholder' => $this->t('offset top'),
      '#default_value' => $config->get('offset_top'),
      '#attributes' => [
        'data-template' => 'position'
      ],
    ];

    $form['template']['overall']['top']['offset_left'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Offset from center'),
      '#placeholder' => $this->t('offset center'),
      '#default_value' => $config->get('offset_left'),
      '#attributes' => [
        'data-template' => 'position'
      ],
    ];

    $form['template']['overall']['top']['scrolling_offset_top'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Scrolling offset top'),
      '#placeholder' => $this->t('scrolling offset top'),
      '#default_value' => $config->get('scrolling_offset_top'),
      '#attributes' => [
        'data-template' => 'position'
      ],
    ];

    $form['template']['overall']['bottom'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container'],
      ],
    ];

    $form['template']['overall']['bottom']['background_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('background_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['overall']['bottom']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['overall']['bottom']['font_family'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Family'),
      '#placeholder' => $this->t('font-family'),
      '#default_value' => $config->get('font_family'),
      '#attributes' => [
        'data-template-css' => 'font-family',
      ],
    ];

    $form['template']['overall']['bottom']['box_shadow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Box Shadow'),
      '#placeholder' => $this->t('box-shadow'),
      '#default_value' => $config->get('box_shadow'),
      '#attributes' => [
        'data-template-css' => 'box-shadow',
      ],
    ];

    $form['template']['overall']['bottom']['footer_background_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Footer Background Color'),
      '#default_value' => $config->get('footer_background_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['overall']['bottom']['footer_border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer Border'),
      '#placeholder' => $this->t('footer-border'),
      '#default_value' => $config->get('footer_border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    // Vertical Tabs Templates.
    $form['template']['vertical_tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-tab',
    ];

    // Pledge Tab.
    $form['template']['tab_pledge_view'] = [
      '#type'  => 'details',
      '#title' => $this->t('Pledge Template'),
      '#group' => 'vertical_tabs',
      '#attributes' => [
        'class' => ['templates-views', 'pledge'],
        'data-template' => 'pledge',
      ],
    ];

//    $form['template']['tab_pledge_view']['horizontal_tabs'] = [
//      '#type' => 'horizontal_tabs',
//      '#default_tab' => 'edit-tab',
//    ];

    $form['template']['tab_pledge_view']['pledge_tabs'] = [
      '#type' => 'radios',
      '#options' => [
        'pledge-ext-salutation' => $this->t('Salutation'),
        'pledge-ext-message' => $this->t('Message'),
        'pledge-ext-user' => $this->t('User'),
        'pledge-ext-button' => $this->t('Button'),
        'pledge-ext-arrow' => $this->t('Arrow'),
      ],
      '#attributes' => [
        'class' => ['horizontal-tabs'],
      ],
    ];

    $form['template']['tab_pledge_view']['salutation'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pledge-ext-salutation'],
        'data-template' => 'salutation',
      ]
    ];

    $form['template']['tab_pledge_view']['salutation']['welcome'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Salutation'),
      '#placeholder' => $this->t('Dear {user},'),
      '#default_value' => $config->get('welcome'),
    ];

    $form['template']['tab_pledge_view']['salutation']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pledge_view']['salutation']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pledge_view']['salutation']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pledge_view']['salutation']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pledge_view']['salutation']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pledge_view']['salutation']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pledge_view']['message'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pledge-ext-message'],
        'data-template' => 'message',
      ]
    ];

    $form['template']['tab_pledge_view']['message']['message_expanded'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Expanded View)'),
      '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
      '#default_value' => $config->get('message_expanded'),
    ];

    $form['template']['tab_pledge_view']['message']['message_collapsed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Collapsed View)'),
      '#placeholder' => $this->t('Please support quality journalism. {pledge-button}'),
      '#default_value' => $config->get('message_collapsed'),
    ];

    $form['template']['tab_pledge_view']['message']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pledge_view']['message']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pledge_view']['message']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pledge_view']['message']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pledge_view']['message']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pledge_view']['message']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pledge_view']['user'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pledge-ext-user'],
        'data-template' => 'user',
      ]
    ];

    $form['template']['tab_pledge_view']['user']['connect_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Connect Message'),
      '#placeholder' => $this->t('Already used us before? {connect_url}'),
      '#default_value' => $config->get('connect_message'),
    ];

    $form['template']['tab_pledge_view']['user']['disconnect_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disconnect Message'),
      '#placeholder' => $this->t('Not {user}? {disconnect_url}'),
      '#default_value' => $config->get('disconnect_message'),
    ];

    $form['template']['tab_pledge_view']['user']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pledge_view']['user']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pledge_view']['user']['weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pledge_view']['user']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pledge_view']['user']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text_align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pledge_view']['user']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pledge_view']['button'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pledge-ext-button'],
        'data-template' => 'button',
      ]
    ];

    $form['template']['tab_pledge_view']['button']['button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Micropayments Button Text'),
      '#placeholder' => $this->t('PLEDGE {price}'),
      '#default_value' => $config->get('button_text'),
    ];

    $form['template']['tab_pledge_view']['button']['button_icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Micropayments Button Icon'),
      '#placeholder' => $this->t('fa-check'),
      '#default_value' => $config->get('button_icon'),
    ];

    $form['template']['tab_pledge_view']['button']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pledge_view']['button']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pledge_view']['arrow'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pledge-ext-arrow'],
        'data-template' => 'arrow',
      ]
    ];

    $form['template']['tab_pledge_view']['arrow']['close_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Closing Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-up'),
      '#default_value' => $config->get('close_arrow'),
    ];

    $form['template']['tab_pledge_view']['arrow']['close_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('close_color'),
    ];

    $form['template']['tab_pledge_view']['arrow']['open_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-down'),
      '#default_value' => $config->get('open_arrow'),
    ];

    $form['template']['tab_pledge_view']['arrow']['open_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('open_color'),
    ];

    $form['template']['tab_pledge_view']['submit_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => ['save-templates'],
        'data-submit' => 'pledge',
      ],
    ];

    // Pay Tab.
    $form['template']['tab_pay_view'] = [
      '#type'  => 'details',
      '#title' => $this->t('Pay Template'),
      '#group' => 'vertical_tabs',
      '#attributes' => [
        'class' => ['templates-views', 'pay'],
        'data-template' => 'pay',
      ],
    ];

    $form['template']['tab_pay_view']['pay_tabs'] = [
      '#type' => 'radios',
      '#options' => [
        'pay-ext-salutation' => $this->t('Salutation'),
        'pay-ext-message' => $this->t('Message'),
        'pay-ext-user' => $this->t('User'),
        'pay-ext-input' => $this->t('Input'),
        'pay-ext-button' => $this->t('Button'),
        'pay-ext-arrow' => $this->t('Arrow'),
      ],
      '#attributes' => [
        'class' => ['horizontal-tabs'],
      ],
    ];

//    $form['template']['tab_pay_view']['horizontal_tabs'] = [
//      '#type' => 'horizontal_tabs',
//      '#default_tab' => 'edit-tab',
//      '#attributes' => [
//        'class' => ['templates-views', 'pay'],
//        'data-template' => 'pay',
//      ],
//    ];

    $form['template']['tab_pay_view']['salutation'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-salutation'],
        'data-template' => 'salutation',
      ],
    ];
    $form['template']['tab_pay_view']['salutation']['welcome'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Salutation'),
      '#placeholder' => $this->t('Dear {user},'),
      '#default_value' => $config->get('welcome'),
    ];

    $form['template']['tab_pay_view']['salutation']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['salutation']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pay_view']['salutation']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pay_view']['salutation']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pay_view']['salutation']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pay_view']['salutation']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pay_view']['message'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-message'],
        'data-template' => 'message',
      ]
    ];

    $form['template']['tab_pay_view']['message']['message_expanded_view'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Expanded View)'),
      '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
      '#default_value' => $config->get('message_expanded_view'),
    ];

    $form['template']['tab_pay_view']['message']['message_collapsed_view'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Collapsed View)'),
      '#placeholder' => $this->t('Please support quality journalism. {pledge-button}'),
      '#default_value' => $config->get('message_collapsed_view'),
    ];

    $form['template']['tab_pay_view']['message']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['message']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pay_view']['message']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pay_view']['message']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font_style',
      ],
    ];

    $form['template']['tab_pay_view']['message']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pay_view']['message']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pay_view']['user'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-user'],
        'data-template' => 'user',
      ]
    ];

    $form['template']['tab_pay_view']['user']['connect_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Connect Message'),
      '#placeholder' => $this->t('Already used us before? {connect_url}'),
      '#default_value' => $config->get('connect_message'),
    ];

    $form['template']['tab_pay_view']['user']['disconnect_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disconnect Message'),
      '#placeholder' => $this->t('Not {user}? {disconnect_url}'),
      '#default_value' => $config->get('disconnect_message'),
    ];

    $form['template']['tab_pay_view']['user']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['user']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pay_view']['user']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font_weight',
      ],
    ];

    $form['template']['tab_pay_view']['user']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pay_view']['user']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pay_view']['user']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pay_view']['input'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-input'],
        'data-template' => 'input',
      ]
    ];

    $form['template']['tab_pay_view']['input']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_pay_view']['input']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_pay_view']['input']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_pay_view']['input']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pay_view']['input']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pay_view']['input']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['input']['box_shadow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Box Shadow'),
      '#placeholder' => $this->t('box-shadow'),
      '#default_value' => $config->get('box_shadow'),
      '#attributes' => [
        'data-template-css' => 'box-shadow',
      ],
    ];

    $form['template']['tab_pay_view']['input']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pay_view']['input']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pay_view']['button'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-button'],
        'data-template' => 'button',
      ]
    ];

    $form['template']['tab_pay_view']['button']['button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Micropayments Button Text'),
      '#placeholder' => $this->t('PLEDGE {price}'),
      '#default_value' => $config->get('button_text'),
    ];

    $form['template']['tab_pay_view']['button']['button_icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Micropayments Button Icon'),
      '#placeholder' => $this->t('fa-check'),
      '#default_value' => $config->get('button_icon'),
    ];

    $form['template']['tab_pay_view']['button']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_pay_view']['button']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_pay_view']['button']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_pay_view']['button']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_pay_view']['button']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_pay_view']['button']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['button']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_pay_view']['button']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_pay_view']['button']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_pay_view']['arrow'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'pay-ext-arrow'],
        'data-template' => 'arrow',
      ]
    ];

    $form['template']['tab_pay_view']['arrow']['close_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Closing Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-up'),
      '#default_value' => $config->get('close_arrow'),
    ];

    $form['template']['tab_pay_view']['arrow']['close_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('close_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['arrow']['open_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-down'),
      '#default_value' => $config->get('open_arrow'),
    ];

    $form['template']['tab_pay_view']['arrow']['open_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('open_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_pay_view']['submit_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => ['save-templates'],
        'data-submit' => 'pay',
      ],
    ];

    // Refund Tab.
    $form['template']['tab_refund_view'] = [
      '#type'  => 'details',
      '#title' => $this->t('Refund Template'),
      '#group' => 'vertical_tabs',
      '#attributes' => [
        'class' => ['templates-views', 'refund'],
        'data-template' => 'refund',
      ],
    ];

//    $form['template']['tab_refund_view']['refundComponent'] = [
//      '#type' => 'text_format',
//      '#format' => $config->get('refundComponent')['format'],
//      '#default_value' => $config->get('refundComponent')['value'],
//      '#rows' => 14,
//    ];

    $form['template']['tab_refund_view']['refund_tabs'] = [
      '#type' => 'radios',
      '#options' => [
        'refund-ext-message' => $this->t('Message'),
        'refund-ext-mood' => $this->t('Mood'),
        'refund-ext-share' => $this->t('Share'),
        'refund-ext-button' => $this->t('Button'),
        'refund-ext-arrow' => $this->t('Arrow'),
      ],
      '#attributes' => [
        'class' => ['horizontal-tabs'],
      ],
    ];

    $form['template']['tab_refund_view']['message'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'refund-ext-message'],
        'data-template' => 'message',
      ],
    ];

    $form['template']['tab_refund_view']['message']['message_expanded_view'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Expanded View)'),
      '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
      '#default_value' => $config->get('message_expanded_view'),
    ];

    $form['template']['tab_refund_view']['message']['message_collapsed_view'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message (Collapsed View)'),
      '#placeholder' => $this->t('Please support quality journalism. {pledge-button}'),
      '#default_value' => $config->get('message_collapsed_view'),
    ];

    $form['template']['tab_refund_view']['message']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['message']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_refund_view']['message']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_refund_view']['message']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_refund_view']['message']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_refund_view']['message']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_refund_view']['mood'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'refund-ext-mood'],
        'data-template' => 'mood',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message'),
      '#placeholder' => $this->t('How do you feel now?'),
      '#default_value' => $config->get('message'),
    ];

    $form['template']['tab_refund_view']['mood']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_color'),
      '#attributes' => [
        'data-template-css' => 'font-color',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['body_feeling_happy'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Happy Mood Text'),
      '#placeholder' => $this->t('happy'),
      '#default_value' => $config->get('body_feeling_happy'),
    ];

    $form['template']['tab_refund_view']['mood']['happy_mood_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Happy Mood Color'),
      '#default_value' => $config->get('happy_mood_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['body_feeling_ok'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Neutral Mood Text'),
      '#placeholder' => $this->t('OK'),
      '#default_value' => $config->get('body_feeling_ok'),
    ];

    $form['template']['tab_refund_view']['mood']['neutral_mood_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Neutral Mood Color'),
      '#default_value' => $config->get('neutral_mood_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['mood']['body_feeling_not_happy'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Not Happy Mood Text'),
      '#placeholder' => $this->t('Not happy'),
      '#default_value' => $config->get('body_feeling_not_happy'),
    ];

    $form['template']['tab_refund_view']['mood']['not_happy_mood_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Not Happy Mood Color'),
      '#default_value' => $config->get('not_happy_mood_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['share'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'refund-ext-share'],
        'data-template' => 'share',
      ],
    ];

    $form['template']['tab_refund_view']['share']['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message'),
      '#placeholder' => $this->t('Share your experience'),
      '#default_value' => $config->get('message'),
    ];

    $form['template']['tab_refund_view']['share']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['share']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_refund_view']['share']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_refund_view']['share']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_refund_view']['share']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_refund_view']['share']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_refund_view']['share']['share_tool_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Share Tool'),
      '#placeholder' => $this->t('fa-facebook'),
      '#default_value' => $config->get('share_tool_1'),
    ];

    $form['template']['tab_refund_view']['share']['share_tool_color_1'] = [
      '#type' => 'color',
      '#title' => $this->t('Share Tool Color'),
      '#default_value' => $config->get('share_tool_color_1'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['share']['share_tool_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Share Tool'),
      '#placeholder' => $this->t('fa-twitter'),
      '#default_value' => $config->get('share_tool_2'),
    ];

    $form['template']['tab_refund_view']['share']['share_tool_color_2'] = [
      '#type' => 'color',
      '#title' => $this->t('Share Tool'),
      '#default_value' => $config->get('share_tool_color_2'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['share']['share_tool_3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Share Tool'),
      '#placeholder' => $this->t('fa-email'),
      '#default_value' => $config->get('share_tool_3'),
    ];

    $form['template']['tab_refund_view']['share']['share_tool_color_3'] = [
      '#type' => 'color',
      '#title' => $this->t('Share Tool Color'),
      '#default_value' => $config->get('share_tool_color_3'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['share']['share_tool_4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Share Tool'),
      '#placeholder' => $this->t('fa-share'),
      '#default_value' => $config->get('share_tool_4'),
    ];

    $form['template']['tab_refund_view']['share']['share_tool_color_4'] = [
      '#type' => 'color',
      '#title' => $this->t('Share Tool Color'),
      '#default_value' => $config->get('share_tool_color_4'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['button'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'refund-ext-button'],
        'data-template' => 'button',
      ],
    ];

    $form['template']['tab_refund_view']['button']['button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Refund Button Text'),
      '#placeholder' => $this->t('REFUND'),
      '#default_value' => $config->get('button_text'),
    ];

    $form['template']['tab_refund_view']['button']['button_icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Refund Button Icon'),
      '#placeholder' => $this->t('fa-money'),
      '#default_value' => $config->get('button_icon'),
    ];

    $form['template']['tab_refund_view']['button']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_refund_view']['button']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_refund_view']['button']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_refund_view']['button']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_refund_view']['button']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_refund_view']['button']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['button']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_refund_view']['button']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_refund_view']['button']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    $form['template']['tab_refund_view']['arrow'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['tab-content', 'refund-ext-arrow'],
        'data-template' => 'arrow',
      ],
    ];

    $form['template']['tab_refund_view']['arrow']['close_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Closing Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-up'),
      '#default_value' => $config->get('close_arrow'),
    ];

    $form['template']['tab_refund_view']['arrow']['close_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('close_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['arrow']['open_arrow'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening Arrow'),
      '#placeholder' => $this->t('fa-chevron-circle-down'),
      '#default_value' => $config->get('open_arrow'),
    ];

    $form['template']['tab_refund_view']['arrow']['open_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('open_color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_refund_view']['submit_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => ['save-templates'],
        'data-submit' => 'refund',
      ],
    ];

    // Other tab.
    $form['template']['tab_other_view'] = [
      '#type'  => 'details',
      '#title' => $this->t('Other Templates'),
      '#group' => 'vertical_tabs',
      '#attributes' => [
        'class' => ['templates-views', 'other'],
        'data-template' => 'other',
      ],
    ];

    // Other > Unlock view.
    $form['template']['tab_other_view']['unlock'] = [
      '#type'  => 'details',
      '#title' => $this->t('Other Templates > Unlock View'),
      '#group' => 'horizontal_tabs',
      '#open' => TRUE,
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs'] = [
      '#type' => 'radios',
      '#options' => [
        'other-unlock' => $this->t('Button'),
      ],
      '#attributes' => [
        'class' => ['horizontal-tabs'],
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unlock Button Text'),
      '#placeholder' => $this->t('UNLOCK CONTENT'),
      '#default_value' => $config->get('button_text'),
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['button_icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unlock Button Icon'),
      '#placeholder' => $this->t('fa-unlock-alt'),
      '#default_value' => $config->get('button_icon'),
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['font_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#placeholder' => $this->t('font-size'),
      '#default_value' => $config->get('font_size'),
      '#attributes' => [
        'data-template-css' => 'font-size',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['text_align'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Align'),
      '#placeholder' => $this->t('text-align'),
      '#default_value' => $config->get('text_align'),
      '#attributes' => [
        'data-template-css' => 'text-align',
      ],
    ];

    $form['template']['tab_other_view']['unlock']['unlock_tabs']['text_transform'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Transform'),
      '#placeholder' => $this->t('text-transform'),
      '#default_value' => $config->get('text_transform'),
      '#attributes' => [
        'data-template-css' => 'text-transform',
      ],
    ];

    // Other > Price view.
    $form['template']['tab_other_view']['price'] = [
      '#type'  => 'details',
      '#title' => $this->t('Other Templates > Price View'),
      '#group' => 'horizontal_tabs',
      '#open' => TRUE,
    ];

    $form['template']['tab_other_view']['price']['price_tabs'] = [
      '#type' => 'radios',
      '#options' => [
        'other-price' => $this->t('Price'),
      ],
      '#attributes' => [
        'class' => ['horizontal-tabs'],
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['price'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price'),
      '#placeholder' => $this->t('{price}'),
      '#default_value' => $config->get('price'),
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('bg_color'),
      '#attributes' => [
        'data-template-css' => 'background-color',
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['border'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border'),
      '#placeholder' => $this->t('border'),
      '#default_value' => $config->get('border'),
      '#attributes' => [
        'data-template-css' => 'border',
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#placeholder' => $this->t('border-radius'),
      '#default_value' => $config->get('border_radius'),
      '#attributes' => [
        'data-template-css' => 'border-radius',
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $config->get('color'),
      '#attributes' => [
        'data-template-css' => 'color',
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['font_style'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Style'),
      '#placeholder' => $this->t('font-style'),
      '#default_value' => $config->get('font_style'),
      '#attributes' => [
        'data-template-css' => 'font-style',
      ],
    ];

    $form['template']['tab_other_view']['price']['price_tabs']['font_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Weight'),
      '#placeholder' => $this->t('font-weight'),
      '#default_value' => $config->get('font_weight'),
      '#attributes' => [
        'data-template-css' => 'font-weight',
      ],
    ];

    $form['template']['tab_other_view']['submit_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => ['save-templates'],
        'data-submit' => 'other',
      ],
    ];

    $form['#attached']['library'][] = 'adtechmedia/adtechmedia.atmTpls';
    $form['#attached']['library'][] = 'adtechmedia/adtechmedia.atmconfig';

    return $form;
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
    $styles = file_get_contents(drupal_get_path('module', 'adtechmedia') . '/css/atm-modal.css');
    $atm_config['styles'] = base64_encode($styles);

    // Update ATM Property settings.
    $client = new AtmClient();
    $client->updateAtmProperty($atm_config);

    parent::submitForm($form, $form_state);
  }

  /**
   * Get template code.
   *
   * @param string $name
   *   Template name.
   *
   * @return mixed
   *   Template markup.
   */
  public static function getTemplate($name) {
    $client = new AtmClient();
    $templates = $client->templatesLoad();

    return $templates[$name];
  }

}
