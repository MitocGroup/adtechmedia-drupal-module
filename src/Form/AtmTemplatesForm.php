<?php

namespace Drupal\atm\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AtmTemplatesForm.
 */
class AtmTemplatesForm extends AtmAbstractForm {

  private $tabsGroup = 'atm_templates';

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'content-templates-form';
  }

  /**
   * Return Form elements.
   *
   * @param string $suffix
   *   Suffix for element keys.
   *
   * @return array
   *   Form elements.
   */
  private function getStylesBlock($suffix = '') {
    return [
      'line1' => [
        '#type' => 'container',

        'color_' . $suffix => [
          '#type' => 'color',
          '#title' => $this->t('Color'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],

        'font-size_' . $suffix => [
          '#type' => 'textfield',
          '#title' => $this->t('Font size'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],

        'font-weight_' . $suffix => [
          '#type' => 'textfield',
          '#title' => $this->t('Font weight'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],
      ],

      'line2' => [
        '#type' => 'container',

        'font-style_' . $suffix => [
          '#type' => 'textfield',
          '#title' => $this->t('Font style'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],

        'text-align_' . $suffix => [
          '#type' => 'textfield',
          '#title' => $this->t('Text align'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],

        'text-transform_' . $suffix => [
          '#type' => 'textfield',
          '#title' => $this->t('Text transform'),
          '#prefix' => '<div class="layout-column layout-column--one-third">',
          '#suffix' => '</div>',
        ],
      ],
    ];
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
    $form[$this->tabsGroup] = [
      '#type' => 'vertical_tabs',
    ];

    $form['pledge'] = array_merge(
      $this->getPledgeTemplateDetailsTab(), $this->getPledgeTemplateContentTab()
    );

    $form['pay'] = array_merge(
      $this->getPayTemplateDetailsTab(), $this->getPayTemplateContentTab()
    );

    $form['refund'] = array_merge(
      $this->getRefundTemplateDetailsTab(), $this->getRefundTemplateContentTab()
    );

    $form['other'] = array_merge(
      $this->getOtherTemplateDetailsTab(), $this->getOtherTemplateContentTab()
    );

    $form['save'] = [
      '#type' => 'button',
      '#value' => t('Save'),
      '#ajax' => [
        'event' => 'click',
        'callback' => [$this, 'saveParams'],
      ],
      '#prefix' => '<div class="clearfix">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * Get detail tab.
   *
   * @return array
   *    Form element.
   */
  private function getPledgeTemplateDetailsTab() {
    return [
      '#type' => 'details',
      '#title' => t('Pledge template'),
      '#group' => $this->tabsGroup,
    ];
  }

  /**
   * Get detail tab.
   *
   * @return array
   *    Form element.
   */
  private function getPayTemplateDetailsTab() {
    return [
      '#type' => 'details',
      '#title' => t('Pay template'),
      '#group' => $this->tabsGroup,
    ];
  }

  /**
   * Get detail tab.
   *
   * @return array
   *    Form element.
   */
  private function getRefundTemplateDetailsTab() {
    return [
      '#type' => 'details',
      '#title' => t('Refund template'),
      '#group' => $this->tabsGroup,
    ];
  }

  /**
   * Get detail tab.
   *
   * @return array
   *    Form element.
   */
  private function getOtherTemplateDetailsTab() {
    return [
      '#type' => 'details',
      '#title' => t('Other template'),
      '#group' => $this->tabsGroup,
    ];
  }

  /**
   * Get content fot tab.
   *
   * @return array
   *   Form element.
   */
  private function getPledgeTemplateContentTab() {
    $tab = [];

    $tab['pledge_preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],
      'view' => [
        '#type' => 'markup',
        '#theme' => 'atm-pledge-template-preview',
      ],
    ];

    $tab['pledge_config'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],

      'salutation' => [
        '#type' => 'details',
        '#title' => $this->t('Salutation'),
        '#open' => TRUE,

        'welcome_pledge-template-salutation' => [
          '#type' => 'textfield',
          '#title' => $this->t('Salutation'),
          '#placeholder' => 'Dear, {user}',
        ],
        [
          $this->getStylesBlock('pledge-template-salutation'),
        ],
      ],

      'message' => [
        '#type' => 'details',
        '#title' => $this->t('Message'),
        '#open' => FALSE,

        'message-expanded_pledge-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Expanded View)'),
          '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
        ],

        'message-collapsed_pledge-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Collapsed View)'),
          '#placeholder' => $this->t('Please support quality journalism.'),
        ],
        [
          $this->getStylesBlock('pledge-template-message'),
        ],
      ],

      'user' => [
        '#type' => 'details',
        '#title' => $this->t('User'),
        '#open' => FALSE,

        'connect-message_pledge-template-user' => [
          '#type' => 'textfield',
          '#title' => $this->t('Connect Message'),
          '#placeholder' => $this->t('Already used us before? {connect_url}'),
        ],

        'disconnect-message_pledge-template-user' => [
          '#type' => 'textfield',
          '#title' => $this->t('Disconnect Message'),
          '#placeholder' => $this->t('Not {user}? {disconnect_url}'),
        ],
        [
          $this->getStylesBlock('pledge-template-user'),
        ],
      ],

      'button' => [
        '#type' => 'details',
        '#title' => $this->t('Button'),
        '#open' => FALSE,
      ],

      'arrow' => [
        '#type' => 'details',
        '#title' => $this->t('Arrow'),
        '#open' => FALSE,
      ],
    ];

    return $tab;
  }

  /**
   * Get content fot tab.
   *
   * @return array
   *   Form element.
   */
  private function getPayTemplateContentTab() {
    $tab = [];

    $tab['pay_preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],
      'view' => [
        '#type' => 'markup',
        '#theme' => 'atm-pay-template-preview',
      ],
    ];

    $tab['pay_config'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],

      'salutation' => [
        '#type' => 'details',
        '#title' => $this->t('Salutation'),
        '#open' => TRUE,

        'welcome_pay-template-salutation' => [
          '#type' => 'textfield',
          '#title' => $this->t('Salutation'),
          '#placeholder' => 'Dear, {user}',
        ],
        [
          $this->getStylesBlock('pay-template-salutation'),
        ],
      ],

      'message' => [
        '#type' => 'details',
        '#title' => $this->t('Message'),
        '#open' => FALSE,

        'message-expanded_pay-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Expanded View)'),
          '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
        ],

        'message-collapsed_pay-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Collapsed View)'),
          '#placeholder' => $this->t('Support quality journalism. {pay-button}'),
        ],
        [
          $this->getStylesBlock('pay-template-message'),
        ],
      ],

      'user' => [
        '#type' => 'details',
        '#title' => $this->t('User'),
        '#open' => FALSE,

        'connect-message_pay-template-user' => [
          '#type' => 'textfield',
          '#title' => $this->t('Connect Message'),
          '#placeholder' => $this->t('Already used us before? {connect_url}'),
        ],

        'disconnect-message_pay-template-user' => [
          '#type' => 'textfield',
          '#title' => $this->t('Disconnect Message'),
          '#placeholder' => $this->t('Not {user}? {disconnect_url}'),
        ],
        [
          $this->getStylesBlock('pay-template-user'),
        ],
      ],

      'input' => [
        '#type' => 'details',
        '#title' => $this->t('Input'),
        '#open' => FALSE,
      ],

      'button' => [
        '#type' => 'details',
        '#title' => $this->t('Button'),
        '#open' => FALSE,
      ],

      'arrow' => [
        '#type' => 'details',
        '#title' => $this->t('Arrow'),
        '#open' => FALSE,
      ],
    ];

    return $tab;
  }

  /**
   * Get content fot tab.
   *
   * @return array
   *   Form element.
   */
  private function getRefundTemplateContentTab() {
    $tab = [];

    $tab['refund_preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],
      'view' => [
        '#type' => 'markup',
        '#theme' => 'atm-refund-template-preview',
      ],
    ];

    $tab['refund_config'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],

      'message' => [
        '#type' => 'details',
        '#title' => $this->t('Message'),
        '#open' => FALSE,

        'message-expanded_refund-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Expanded View)'),
          '#placeholder' => $this->t('Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?'),
        ],

        'message-collapsed_refund-template-message' => [
          '#type' => 'textfield',
          '#title' => $this->t('Message (Collapsed View)'),
          '#placeholder' => $this->t('Support quality journalism. {pay-button}'),
        ],
        [
          $this->getStylesBlock('refund-template-message'),
        ],
      ],

      'mood' => [
        '#type' => 'details',
        '#title' => $this->t('Mood'),
        '#open' => FALSE,
      ],

      'share' => [
        '#type' => 'details',
        '#title' => $this->t('Share'),
        '#open' => FALSE,
      ],

      'button' => [
        '#type' => 'details',
        '#title' => $this->t('Button'),
        '#open' => FALSE,
      ],

      'arrow' => [
        '#type' => 'details',
        '#title' => $this->t('Arrow'),
        '#open' => FALSE,
      ],
    ];

    return $tab;
  }

  /**
   * Get content fot tab.
   *
   * @return array
   *   Form element.
   */
  private function getOtherTemplateContentTab() {
    $tab = [];

    $tab['unlock-view'] = [
      '#type' => 'fieldset',
      '#title' => t('Unlock view'),
    ];

    $tab['price-view'] = [
      '#type' => 'fieldset',
      '#title' => t('Price view'),
    ];

    return $tab;
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
   *   Ajax Response.
   */
  public function saveParams(array &$form, FormStateInterface $form_state) {
    return new AjaxResponse();
  }

}
