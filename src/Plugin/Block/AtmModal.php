<?php

namespace Drupal\adtechmedia\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'AtmModal' block.
 *
 * @Block(
 *  id = "atm_modal",
 *  admin_label = @Translation("ATM Modal"),
 * )
 */
class AtmModal extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'description' => [
        'value' => $this->t('Support quality journalism. Get involved and PLEDGE 5¢ now.'),
        'format' => 'full_html',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Add description for ATM modal.'),
      '#default_value' => $this->configuration['description']['value'],
      '#format' => $this->configuration['description']['format'],
      '#weight' => '10',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['description'] = $form_state->getValue('description');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'atm_modal',
    ];

    // Add ATM.js library.
    $build['#attached']['library'][] = 'adtechmedia/adtechmedia.atmjs';

    return $build;
  }

}
