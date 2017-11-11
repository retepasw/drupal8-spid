<?php

namespace Drupal\spid_pasw\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\spid_pasw\Service\SpidPaswManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class SimplesamlSubscriber implements EventSubscriberInterface {

  /**
   * The SimpleSAML Authentication helper service.
   *
   * @var \Drupal\spid_pasw\Service\SpidPaswManager
   */
  protected $simplesaml;

  /**
   * The current account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;


  /**
   * {@inheritdoc}
   *
   * @param SpidPaswManager $simplesaml
   *   The SimpleSAML Authentication helper service.
   * @param AccountInterface $account
   *   The current account.
   * @param ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(SpidPaswManager $simplesaml, AccountInterface $account, ConfigFactoryInterface $config_factory, LoggerInterface $logger) {
    $this->simplesaml = $simplesaml;
    $this->account = $account;
    $this->config = $config_factory->get('spid_pasw.settings');
    $this->logger = $logger;
  }

  /**
   * Logs out user if not SAML authenticated and local logins are disabled.
   *
   * @param GetResponseEvent $event
   *   The subscribed event.
   */
  public function checkAuthStatus(GetResponseEvent $event) {
    if ($this->account->isAnonymous()) {
      return;
    }

    if (!$this->simplesaml->isActivated()) {
      return;
    }

    if ($this->simplesaml->isAuthenticated()) {
      return;
    }

    if ($this->config->get('allow.default_login')) {

      $allowed_uids = explode(',', $this->config->get('allow.default_login_users'));
      if (in_array($this->account->id(), $allowed_uids)) {
        return;
      }

      $allowed_roles = $this->config->get('allow.default_login_roles');
      if (array_intersect($this->account->getRoles(), $allowed_roles)) {
        return;
      }
    }

    if ($this->config->get('debug')) {
      $this->logger->debug('User %name not authorized to log in using local account.', ['%name' => $this->account->getAccountName()]);
    }
    user_logout();

    $response = new RedirectResponse('/', RedirectResponse::HTTP_FOUND);
    $event->setResponse($response);
    $event->stopPropagation();

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkAuthStatus'];
    return $events;
  }

}
