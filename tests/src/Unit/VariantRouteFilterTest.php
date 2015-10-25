<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\VariantRouteFilterTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Display\ContextAwareVariantInterface;
use Drupal\Core\Display\VariantInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\page_manager\PageExecutableInterface;
use Drupal\page_manager\PageInterface;
use Drupal\page_manager\Routing\VariantRouteFilter;
use Drupal\Tests\UnitTestCase;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @coversDefaultClass \Drupal\page_manager\Routing\VariantRouteFilter
 * @group PageManager
 */
class VariantRouteFilterTest extends UnitTestCase {

  /**
   * The mocked entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $entityManager;

  /**
   * The mocked page storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $pageStorage;

  /**
   * The mocked current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $currentPath;

  /**
   * The route filter under test.
   *
   * @var \Drupal\page_manager\Routing\VariantRouteFilter
   */
  protected $routeFilter;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->pageStorage = $this->prophesize(ConfigEntityStorageInterface::class);

    $this->entityManager = $this->prophesize(EntityManagerInterface::class);
    $this->entityManager->getStorage('page')
      ->willReturn($this->pageStorage);
    $this->currentPath = $this->prophesize(CurrentPathStack::class);

    $this->routeFilter = new VariantRouteFilter($this->entityManager->reveal(), $this->currentPath->reveal());
  }

  /**
   * @covers ::applies
   *
   * @dataProvider providerTestApplies
   */
  public function testApplies($options, $expected) {
    $route = new Route('/path/with/{slug}', [], [], $options);
    $result = $this->routeFilter->applies($route);
    $this->assertSame($expected, $result);
  }

  public function providerTestApplies() {
    $data = [];
    $data['no_options'] = [[], FALSE];
    $data['with_options'] = [['parameters' => ['page_manager_page' => TRUE]], TRUE];
    return $data;
  }

  /**
   * @covers ::filter
   */
  public function testFilterEmptyCollection() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $this->currentPath->getPath($request)->shouldNotBeCalled();

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = [];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterContextException() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'a_variant']);
    $route_collection->add('a_route', $route);

    $variant = $this->prophesize(VariantInterface::class);
    $variant->access()->willThrow(new ContextException());

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('a_variant')->willReturn($variant);

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('a_page')->willReturn($page->reveal());

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = [];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterNonMatchingRoute() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route = new Route('/path/with/{slug}');
    $route_collection->add('a_route', $route);

    $this->currentPath->getPath($request)->willReturn('');

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['a_route' => $route];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterDeniedAccess() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'a_variant']);
    $route_collection->add('a_route', $route);

    $variant = $this->prophesize(VariantInterface::class);
    $variant->access()->willReturn(FALSE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('a_variant')->willReturn($variant);

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('a_page')->willReturn($page->reveal());

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = [];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterAllowedAccess() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'a_variant']);
    $route_collection->add('a_route', $route);

    $variant = $this->prophesize(ContextAwareVariantInterface::class);
    $variant->access()->willReturn(TRUE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('a_variant')->willReturn($variant);

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('a_page')->willReturn($page->reveal());

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['a_route' => $route];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterAllowedAccessTwoRoutes() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route1 = new Route('/path/with/{slug}', ['page_manager_page' => 'page_1', 'variant_id' => 'variant_1']);
    $route2 = new Route('/path/with/{slug}', ['page_manager_page' => 'page_2', 'variant_id' => 'variant_2']);
    $route_collection->add('route_1', $route1);
    $route_collection->add('route_2', $route2);

    $variant = $this->prophesize(ContextAwareVariantInterface::class);
    $variant->access()->willReturn(TRUE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('variant_1')->willReturn($variant);
    $executable->getRuntimeVariant('variant_2')->shouldNotBeCalled();

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('page_1')->willReturn($page->reveal());
    $this->pageStorage->load('page_2')->shouldNotBeCalled();

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['route_1' => $route1];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   */
  public function testFilterAllowedAccessSecondRoute() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route1 = new Route('/path/with/{slug}', ['page_manager_page' => 'page_1', 'variant_id' => 'variant_1']);
    $route2 = new Route('/path/with/{slug}', ['page_manager_page' => 'page_2', 'variant_id' => 'variant_2']);
    $route_collection->add('route_1', $route1);
    $route_collection->add('route_2', $route2);

    $variant1 = $this->prophesize(VariantInterface::class);
    $variant1->access()->willReturn(FALSE);
    $variant2 = $this->prophesize(VariantInterface::class);
    $variant2->access()->willReturn(TRUE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('variant_1')->willReturn($variant1);
    $executable->getRuntimeVariant('variant_2')->willReturn($variant2);

    $page1 = $this->prophesize(PageInterface::class);
    $page1->getExecutable()->willReturn($executable->reveal());

    $page2 = $this->prophesize(PageInterface::class);
    $page2->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('page_1')->willReturn($page1->reveal())->shouldBeCalled();
    $this->pageStorage->load('page_2')->willReturn($page2->reveal())->shouldBeCalled();

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['route_2' => $route2];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   * @covers ::processRoute
   *
   * Tests when the first page_manager route is allowed, but other
   * non-page_manager routes are also present.
   */
  public function testFilterAllowedAccessFirstRoute() {
    $route_collection = new RouteCollection();
    $request = new Request();

    $route1 = new Route('/path/with/{slug}');
    $route2 = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'variant1']);
    $route3 = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'variant2']);
    $route4 = new Route('/path/with/{slug}');
    $route_collection->add('route_1', $route1);
    $route_collection->add('route_2', $route2);
    $route_collection->add('route_3', $route3);
    $route_collection->add('route_4', $route4);

    $variant1 = $this->prophesize(VariantInterface::class);
    $variant1->access()->willReturn(TRUE);
    $variant2 = $this->prophesize(VariantInterface::class);
    $variant2->access()->willReturn(FALSE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('variant1')->willReturn($variant1);
    $executable->getRuntimeVariant('variant2')->willReturn($variant2);

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('a_page')->willReturn($page->reveal())->shouldBeCalled();

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['route_2' => $route2, 'route_1' => $route1, 'route_4' => $route4];
    $this->assertSame($expected, $result->all());
    $this->assertSame([], $request->attributes->all());
  }

  /**
   * @covers ::filter
   */
  public function testFilterRequestAttributes() {
    $route_collection = new RouteCollection();
    $request = new Request([], [], ['foo' => 'bar']);

    $route = new Route('/path/with/{slug}', ['page_manager_page' => 'a_page', 'variant_id' => 'a_variant']);
    $route_collection->add('a_route', $route);

    $variant = $this->prophesize(ContextAwareVariantInterface::class);
    $variant->access()->willReturn(TRUE);

    $executable = $this->prophesize(PageExecutableInterface::class);
    $executable->getRuntimeVariant('a_variant')->willReturn($variant);

    $page = $this->prophesize(PageInterface::class);
    $page->getExecutable()->willReturn($executable->reveal());

    $this->currentPath->getPath($request)->willReturn('');
    $this->pageStorage->load('a_page')->willReturn($page->reveal());

    $result = $this->routeFilter->filter($route_collection, $request);
    $expected = ['a_route' => $route];
    $this->assertSame($expected, $result->all());
    $this->assertSame(['foo' => 'bar'], $request->attributes->all());
  }

  /**
   * @covers ::getRequestAttributes
   */
  public function testGetRequestAttributes() {
    $request = new Request();

    $route = new Route('/path/with/{slug}');
    $route_name = 'a_route';

    $this->currentPath->getPath($request)->willReturn('/path/with/1');

    $expected_attributes = ['slug' => 1, '_route_object' => $route, '_route' => $route_name];
    $route_enhancer = $this->prophesize(RouteEnhancerInterface::class);
    $route_enhancer->enhance($expected_attributes, $request)->willReturn(['slug' => 'slug 1']);
    $this->routeFilter->addRouteEnhancer($route_enhancer->reveal());

    $this->assertSame([], $request->attributes->all());

    $method = new \ReflectionMethod($this->routeFilter, 'getRequestAttributes');
    $method->setAccessible(TRUE);
    $attributes = $method->invoke($this->routeFilter, $route, $route_name, $request);

    $this->assertSame(['slug' => 'slug 1'], $attributes);
  }

}
