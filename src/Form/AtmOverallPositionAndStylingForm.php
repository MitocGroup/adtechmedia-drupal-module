<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class AtmOverallPositionAndStylingForm.
 */
class AtmOverallPositionAndStylingForm extends AtmAbstractForm {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'atm-overall-position-and-styling-form';
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
    $form['#attached']['library'][] = 'atm/api.admin';

    $form['sticky'] = [
      '#type' => 'checkbox',
      '#title' => t('Sticky'),
      '#default_value' => $this->getHelper()->get('target-cb-sticky'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['width'] = [
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['offset-top'] = [
      '#type' => 'textfield',
      '#title' => t('Offset top'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['offset-left'] = [
      '#type' => 'textfield',
      '#title' => t('Offset left'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['scrolling-offset-top'] = [
      '#type' => 'textfield',
      '#title' => t('Scrolling Offset Top'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['background-color'] = [
      '#type' => 'color',
      '#title' => t('Background Color'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['border'] = [
      '#type' => 'textfield',
      '#title' => t('Border'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['font-family'] = [
      '#type' => 'textfield',
      '#title' => t('Font family'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['box-shadow'] = [
      '#type' => 'textfield',
      '#title' => t('Box shadow'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['footer-background-color'] = [
      '#type' => 'color',
      '#title' => t('Footer Background Color'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['footer-border'] = [
      '#type' => 'textfield',
      '#title' => t('Footer border'),
      '#prefix' => '<div class="layout-column layout-column--one-sixth">',
      '#suffix' => '</div>',
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#ajax' => [
        'event' => 'click',
      ],
      '#prefix' => '<div class="clearfix">',
      '#suffix' => '</div>',
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
    $values = $form_state->getValues();

    $this->getHelper()->set('target-cb-sticky', $values['sticky']);
  }

}
