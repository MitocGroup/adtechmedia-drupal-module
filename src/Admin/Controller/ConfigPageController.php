<?php

namespace Drupal\atm\Admin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides routers for admin area controller atm.
 */
class ConfigPageController extends ControllerBase {

  /**
   * Return content for config page ATM.
   */
  public function content() {

    return [
      '#theme' => 'atm-admin-config-page',
      '#content_type_select' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('Select Content Type'),
          'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmContentTypeSelectForm'),
        ],
      ],
      '#general_configuration' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('General Configuration'),
          'content' => [
            [
              '#theme' => 'container',
              '#attributes' => [
                'class' => 'clearfix',
              ],
              '#children' => [
                [
                  '#theme' => 'container',
                  '#attributes' => [
                    'class' => 'layout-column layout-column--half',
                  ],
                  '#children' => [
                    [
                      '#theme' => 'admin_block',
                      '#block' => [
                        'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmGeneralConfigForm'),
                      ],
                    ],
                  ],
                ],
                [
                  '#theme' => 'container',
                  '#attributes' => [
                    'class' => 'layout-column layout-column--half',
                  ],
                  '#children' => [
                    [
                      '#theme' => 'admin_block',
                      '#block' => [
                        'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmRegisterForm'),
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
      '#content_configuration' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('Content configuration'),
          'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmContentConfigurationForm'),
        ],
      ],
      '#templates_management' => [
        '#theme' => 'admin_block',
        '#block' => [
          'title' => t('Templates management'),
          'content' => [
            'overall_position_and_styling' => [
              '#theme' => 'admin_block',
              '#block' => [
                'title' => t('Overall Position And Styling'),
                'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmOverallStylingAndPositionForm'),
              ],
            ],
            'content_templates' => [
              '#theme' => 'admin_block',
              '#block' => [
                'content' => \Drupal::formBuilder()->getForm('\Drupal\atm\Form\AtmTemplatesForm'),
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
