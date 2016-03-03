<?php

/**
 * @file
 * Contains \Drupal\Tests\page_manager\Unit\DisplayVariantTest.
 */

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\page_manager\ContextMapperInterface;
use Drupal\panels\Entity\DisplayVariant;
use Drupal\page_manager\PageInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\panels\Entity\DisplayVariant
 *
 * @group PageManager
 */
class DisplayVariantTest extends UnitTestCase {

  /**
   * @var \Drupal\panels\Entity\DisplayVariant
   */
  protected $displayVariant;

  /**
   * @var \Drupal\page_manager\PageInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $page;

  /**
   * @var \Drupal\page_manager\ContextMapperInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $contextMapper;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->displayVariant = new DisplayVariant(['id' => 'the_display_variant', 'display_entity_type' => 'page', 'display_entity_id' => 'the_page'], 'display_variant');
    $this->page = $this->prophesize(PageInterface::class);

    $entity_storage = $this->prophesize(EntityStorageInterface::class);
    $entity_storage->load('the_page')->willReturn($this->page->reveal());

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('page')->willReturn($entity_storage);

    $this->contextMapper = $this->prophesize(ContextMapperInterface::class);

    $container = new ContainerBuilder();
    $container->set('entity_type.manager', $entity_type_manager->reveal());
    $container->set('page_manager.context_mapper', $this->contextMapper->reveal());
    \Drupal::setContainer($container);
  }

  /**
   * @covers ::getContexts
   * @dataProvider providerTestGetContexts
   */
  public function testGetContexts($static_contexts, $page_contexts, $expected) {
    $this->contextMapper->getContextValues([])->willReturn($static_contexts)->shouldBeCalledTimes(1);
    $this->page->getContexts()->willReturn($page_contexts)->shouldBeCalledTimes(1);

    $contexts = $this->displayVariant->getContexts();
    $this->assertSame($expected, $contexts);
    $contexts = $this->displayVariant->getContexts();
    $this->assertSame($expected, $contexts);
  }

  public function providerTestGetContexts() {
    $data = [];
    $data['empty'] = [
      [],
      [],
      [],
    ];
    $data['additive'] = [
      ['static' => 'static'],
      ['page' => 'page'],
      ['static' => 'static', 'page' => 'page'],
    ];
    $data['conflicting'] = [
      ['foo' => 'static'],
      ['foo' => 'page'],
      ['foo' => 'page'],
    ];
    return $data;
  }

  /**
   * @covers ::getContexts
   * @covers ::removeStaticContext
   */
  public function testGetContextsAfterReset() {
    $this->contextMapper->getContextValues([])->willReturn([])->shouldBeCalledTimes(2);
    $this->page->getContexts()->willReturn([])->shouldBeCalledTimes(2);

    $expected = [];
    $contexts = $this->displayVariant->getContexts();
    $this->assertSame($expected, $contexts);
    $this->displayVariant->removeStaticContext('anything');
    $contexts = $this->displayVariant->getContexts();
    $this->assertSame($expected, $contexts);
  }

}
