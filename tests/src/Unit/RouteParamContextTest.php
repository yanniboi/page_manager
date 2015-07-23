<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\RouteParamContextTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\page_manager\EventSubscriber\RouteParamContext;
use Drupal\page_manager\PageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Tests the route param context.
 *
 * @coversDefaultClass \Drupal\page_manager\EventSubscriber\RouteParamContext
 *
 * @group PageManager
 */
class RouteParamContextTest extends PageContextTestBase {

  public function testOnPageContext() {
    $collection = new RouteCollection();
    $route_provider = $this->getMock(RouteProviderInterface::class);
    $route_provider->expects($this->once())
      ->method('getRoutesByPattern')
      ->will($this->returnValue($collection));
    $request = new Request();
    $request_stack = new RequestStack();
    $request_stack->push($request);

    $this->typedDataManager->expects($this->any())
      ->method('getDefaultConstraints')
      ->will($this->returnValue([]));
    $this->typedDataManager->expects($this->once())
      ->method('create')
      ->with($this->isType('object'), 'banana')
      ->will($this->returnValue($this->getMock(TypedDataInterface::class)));
    $this->typedDataManager->expects($this->any())
      ->method('createDataDefinition')
      ->will($this->returnCallback(function ($type) {
        return new DataDefinition(['type' => $type]);
      }));

    $page = $this->getMock(PageInterface::class);
    $this->executable->expects($this->once())
      ->method('getPage')
      ->will($this->returnValue($page));
    $page->expects($this->once())
      ->method('getPath')
      ->will($this->returnValue('/test_route'));

    $this->executable->expects($this->at(1))
      ->method('addContext')
      ->with('foo', $this->isInstanceOf(Context::class));
    $this->executable->expects($this->at(2))
      ->method('addContext')
      ->with('baz', $this->isInstanceOf(Context::class));

    $collection->add('test_route', new Route('/test_route', [], [], [
      'parameters' => [
        'foo' => ['type' => 'bar'],
        'baz' => ['type' => 'bop'],
        'page' => ['type' => 'entity:page']
      ],
    ]));

    // Set up a request with one of the expected parameters as an attribute.
    $request->attributes->add(['foo' => 'banana']);

    $route_param_context = new RouteParamContext($route_provider, $request_stack);
    $route_param_context->onPageContext($this->event);
  }

}
