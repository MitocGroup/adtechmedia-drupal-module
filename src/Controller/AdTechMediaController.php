<?php

namespace Drupal\adtechmedia\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AdTechMedia config page controller.
 */
class AdTechMediaController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function atmConfigPage() {
    $build['config_form'] = $this->formBuilder()
      ->getForm('\Drupal\adtechmedia\Form\AdTechMediaConfigForm');

    $build['management_form'] = $this->formBuilder()
      ->getForm('\Drupal\adtechmedia\Form\AdTechMediaTemplateForm');

    return $build;
  }

}
