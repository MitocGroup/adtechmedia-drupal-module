<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class AtmTemplatesForm.
 */
class AtmTemplatesForm extends AtmAbstractForm {

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
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['pledge'] = $this->getPledgeTemplateDetailsTab();
    $form['pay'] = $this->getPayTemplateDetailsTab();
    $form['refund'] = $this->getRefundTemplateDetailsTab();
    $form['other'] = $this->getOtherTemplateDetailsTab();

    $form['other']['unlock-view'] = [
      '#type' => 'fieldset',
      '#title' => t('Unlock view'),
    ];

    $form['other']['price-view'] = [
      '#type' => 'fieldset',
      '#title' => t('Price view'),
    ];

    $form['pledge'][] = $this->getPledgeTemplateContentTab();
    $form['pay'][] = $this->getPayTemplateContentTab();
    $form['refund'][] = $this->getRefundTemplateContentTab();

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
      '#group' => 'tabs',
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
      '#group' => 'tabs',
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
      '#group' => 'tabs',
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
      '#group' => 'tabs',
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
        '#markup' => 'dadsad',
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
      ],
      'message' => [
        '#type' => 'details',
        '#title' => $this->t('Message'),
        '#open' => FALSE,
      ],
      'user' => [
        '#type' => 'details',
        '#title' => $this->t('User'),
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
  private function getPayTemplateContentTab() {
    $tab = [];

    $tab['pay_preview'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-column', 'layout-column--half'],
      ],
      'view' => [
        '#type' => 'markup',
        '#markup' => 'asasA dadsad',
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
      ],
      'message' => [
        '#type' => 'details',
        '#title' => $this->t('Message'),
        '#open' => FALSE,
      ],
      'user' => [
        '#type' => 'details',
        '#title' => $this->t('User'),
        '#open' => FALSE,
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
        '#markup' => 'asasA dadsad',
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
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
