<?php

namespace Drupal\atm\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class DefaultSubscriber.
 *
 * @package Drupal\atm
 */
class DefaultSubscriber implements EventSubscriberInterface {

  /**
   * Return AtmApiHelper.
   *
   * @return \Drupal\atm\Helper\AtmApiHelper
   *   Return AtmApiHelper.
   */
  protected function getHelper() {
    return \Drupal::service('atm.helper');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => 'onModuleInit',
    ];
  }

  /**
   * Init. Check if api exists.
   */
  public function onModuleInit($events) {
    $apiKey = $this->getHelper()->getApiKey();

    if (empty($apiKey)) {
      $this->getHelper()->generateApiKey();
    }
  }

}
