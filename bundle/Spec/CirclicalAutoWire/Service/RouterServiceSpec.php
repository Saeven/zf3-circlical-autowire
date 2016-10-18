<?php

namespace Spec\CirclicalAutoWire\Service;

use Spec\CirclicalAutoWire\Controller\AnnotatedController;
use Spec\CirclicalAutoWire\Controller\SimpleController;
use CirclicalAutoWire\Service\RouterService;
use PhpSpec\ObjectBehavior;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Router\Http\TreeRouteStack;

class RouterServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouterService::class);
    }

    function let(TreeRouteStack $routeStack)
    {
        $this->beConstructedWith($routeStack, false);
    }

    function it_parses_controllers_by_class()
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/SimpleController.php';
        $this->parseController(SimpleController::class);
    }

    function it_parses_controllers_with_annotations($routeStack)
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/AnnotatedController.php';

        $routeStack->addRoute('baseroute1', [
            'type' => Literal::class,
            'options' => [
                'route' => '/admin/config/foobar',
                'defaults' => [
                    'controller' => AnnotatedController::class,
                    'action' => 'foo',
                ],
            ],
        ])->shouldBeCalled();

        $routeStack->addRoute('baseroute2', [
            'type' => Segment::class,
            'options' => [
                'route' => '/admin/config/foo/:param1/:param2',
                'defaults' => [
                    'controller' => AnnotatedController::class,
                    'action' => 'routeParam',
                ],
                'constraints' => [
                    'param1' => "\\d",
                    'param2' => "A-Za-z",
                ],
            ],
        ])->shouldBeCalled();
        $this->parseController(AnnotatedController::class);
    }
}
