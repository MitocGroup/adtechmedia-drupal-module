<?php

namespace Drupal\adtechmedia;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Configure and manage ATM templates.
 */
class AtmTpls {

  public function updateTpls() {
    $request = \Drupal::request()->request;
    $component = $request->get('component');
    $template = $request->get('template');
    $styles = $request->get('styles');


    $config = [
      'templates' => [
        $component . 'Component' => base64_encode(stripslashes($template)),
      ],
      'styles' => ['main' => base64_encode($styles)],
    ];

    $client = new AtmClient();
    $client->updateAtmPropertyTemplate($config);

    return new JsonResponse("{}");
  }

}
