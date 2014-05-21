<?php

/**
 * @file
 * Contains \Drupal\block_page\EventSubscriber\RouteParamContext.
 */

namespace Drupal\block_page\EventSubscriber;

use Drupal\block_page\Event\BlockPageContextEvent;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TypedData\TypedDataManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Sets values from the route parameters as a context.
 */
class RouteParamContext implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProvider
   */
  protected $routeProvider;

  /**
   * The typed data manager.
   *
   * @var \Drupal\Core\TypedData\TypedDataManager
   */
  protected $typedDataManager;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new CurrentUserContext.
   *
   * @param \Drupal\Core\Routing\RouteProvider $route_provider
   *   The route provider.
   * @param \Drupal\Core\TypedData\TypedDataManager $typed_data_manager
   *   The typed data manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RouteProvider $route_provider, TypedDataManager $typed_data_manager, RequestStack $request_stack) {
    $this->routeProvider = $route_provider;
    $this->typedDataManager = $typed_data_manager;
    $this->requestStack = $request_stack;
  }

  /**
   * Adds in the current user as a context.
   *
   * @param \Drupal\block_page\Event\BlockPageContextEvent $event
   *   The block page context event.
   */
  public function onBlockPageContext(BlockPageContextEvent $event) {
    $request = $this->requestStack->getCurrentRequest();
    $block_page = $event->getBlockPage();
    $routes = $this->routeProvider->getRoutesByPattern('/' . $block_page->getPath())->all();
    $route = reset($routes);

    if ($route_contexts = $route->getOption('parameters')) {
      foreach ($route_contexts as $route_context_name => $route_context) {
        // Skip this parameter.
        if ($route_context_name == 'block_page') {
          continue;
        }

        // @todo Why is array('type' => 'entity:user') different than
        //   array('type' => 'entity', 'constraints' => array('EntityType' => 'user'))
        //   and which one is correct?
        // Add in the definition in order to get the label and constraints.
        $route_context += $this->typedDataManager->getDefinition($route_context['type']);

        // Convert one style of definition to the other.
        if (strpos($route_context['type'], 'entity:') === 0) {
          list($type) = explode(':', $route_context['type'], 2);
          $route_context['type'] = $type;
        }

        $context = new Context($route_context);
        if ($request->attributes->has($route_context_name)) {
          $context->setContextValue($request->attributes->get($route_context_name));
        }
        else {
          // @todo Find a way to add in a fake value for configuration.
        }
        $block_page->addContext($route_context_name, $context);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['block_page_context'][] = 'onBlockPageContext';
    return $events;
  }

}
