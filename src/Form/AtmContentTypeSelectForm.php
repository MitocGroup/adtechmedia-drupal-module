<?php

namespace Drupal\atm\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Class AtmContentTypeSelectForm.
 *
 * @package Drupal\atm\Form
 */
class AtmContentTypeSelectForm extends AtmAbstractForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atm-content-type-select-form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['content-types'] = [
      '#type' => 'checkboxes',
      '#options' => [],
      '#default_value' => $this->getHelper()->getSelectedContentTypes(),
    ];

    /** @var NodeType $nodeType */
    foreach (NodeType::loadMultiple() as $nodeType) {
      $form['content-types']['#options'][$nodeType->id()] = $nodeType->get('name');
    }

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
   * {@inheritdoc}
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
   */
  public function saveParams(array &$form, FormStateInterface $form_state) {
    $selectedCT = [];
    $cTypes = $form_state->getValue('content-types');
    foreach ($cTypes as $key => $value) {
      if ($value) {
        $selectedCT[] = $value;
      }
    }

    $this->getHelper()->set('selected-ct', $selectedCT);
  }

}
