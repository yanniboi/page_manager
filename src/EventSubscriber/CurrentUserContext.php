<?php

/**
 * @file
 * Contains \Drupal\page_manager\EventSubscriber\CurrentUserContext.
 */

namespace Drupal\page_manager\EventSubscriber;

use Drupal\page_manager\Event\PageManagerContextEvent;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sets the current user as a context.
 */
class CurrentUserContext implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The account proxy.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Constructs a new CurrentUserContext.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   *   The account proxy.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(AccountProxyInterface $account_proxy, EntityManagerInterface $entity_manager) {
    $this->accountProxy = $account_proxy;
    $this->userStorage = $entity_manager->getStorage('user');
  }

  /**
   * Adds in the current user as a context.
   *
   * @param \Drupal\page_manager\Event\PageManagerContextEvent $event
   *   The page entity context event.
   */
  public function onPageContext(PageManagerContextEvent $event) {
    $current_user = $this->userStorage->load($this->accountProxy->getAccount()->id());

    // @todo Remove constraints and change type to 'entity:user' once
    //   https://drupal.org/node/2272161 is in.
    $context = new Context(array(
      'type' => 'entity',
      'constraints' => array('EntityType' => 'user'),
      'label' => $this->t('Current user'),
    ));
    $context->setContextValue($current_user);
    $event->getPage()->addContext('current_user', $context);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['page_manager_context'][] = 'onPageContext';
    return $events;
  }

}
