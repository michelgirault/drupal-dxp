<?php

namespace Drupal\unomi;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UnomiCookieManager maintains cookie values.
 *
 * @package Drupal\unomi
 */
class UnomiCookieManager {

  /**
   * The Dropsolid Personalization config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $unomiConfig;

  /**
   * The current request object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * UnomiCookieManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestStack $requestStack) {
    $this->unomiConfig = $config_factory->get('unomi.settings');
    $this->requestStack = $requestStack;
  }

  /**
   * Return the cookie values.
   *
   * When no segment is detected/returned from the cloud function, a cookie
   * with value '_unknown' is set. If no cookie is set, that functionally means
   * the same. So any falsy value gets cast to '_unknown'.
   *
   * @return array
   */
  public function getSegmentCookieValues() {
    $cookieValue = $this->requestStack->getCurrentRequest()->cookies->get($this->getCookieName(), '_unknown');
    if (!$cookieValue) {
      $cookieValue = ['_unknown'];
    }
    $cookieValues = explode(", ", $cookieValue);
    return $cookieValues;
  }

  /**
   * Returns the cookie name.
   *
   * If cookie name is empty returns default value DS_VARY_ML.
   *
   * @return string
   *   Cookie Name.
   */
  public function getCookieName() {
    if (empty($this->unomiConfig->get('cookie_name'))) {
      return 'DS_VARY_ML';
    }
    return $this->unomiConfig->get('cookie_name');
  }

}
