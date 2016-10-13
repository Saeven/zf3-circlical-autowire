<?php

namespace Spec\CirclicalAutoWire\Annotations;

use CirclicalAutoWire\Annotations\Route;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class RouteSpec extends ObjectBehavior
{
    const DUMMY_CONTROLLER = 'C';
    const DUMMY_METHOD = 'M';

    function it_is_initializable()
    {
        $this->shouldHaveType(Route::class);
    }

    function it_processes_literal_routes()
    {
        $this->value = "/foo";
        $routeConfig = $this->transform(self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig->shouldBeLike([
            'type' => Literal::class,
            'options' => [
                'route' => '/foo',
                'defaults' => [
                    'controller' => self::DUMMY_CONTROLLER,
                    'action' => self::DUMMY_METHOD,
                ],
            ],
        ]);
    }

    function it_processes_routes_with_parameters()
    {
        $this->value = "/foo/:param1";
        $routeConfig = $this->transform(self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig->shouldBeLike([
            'type' => Segment::class,
            'options' => [
                'route' => '/foo/:param1',
                'defaults' => [
                    'controller' => self::DUMMY_CONTROLLER,
                    'action' => self::DUMMY_METHOD,
                ],
            ],
        ]);
    }

    function it_processes_routes_with_constraints()
    {
        $this->value = "/foo/:param1";
        $this->constraints = ['param1' => "A-Za-z"];
        $routeConfig = $this->transform(self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig->shouldBeLike([
            'type' => Segment::class,
            'options' => [
                'route' => '/foo/:param1',
                'defaults' => [
                    'controller' => self::DUMMY_CONTROLLER,
                    'action' => self::DUMMY_METHOD,
                ],
                'constraints' => [
                    'param1' => 'A-Za-z',
                ],
            ],
        ]);
    }

    function it_processes_routes_with_defaults()
    {
        $this->value = "/foo/:param1";
        $this->constraints = ['param1' => "A-Za-z"];
        $this->defaults = ['param1' => 'index'];
        $routeConfig = $this->transform(self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig->shouldBeLike([
            'type' => Segment::class,
            'options' => [
                'route' => '/foo/:param1',
                'defaults' => [
                    'controller' => self::DUMMY_CONTROLLER,
                    'action' => self::DUMMY_METHOD,
                ],
                'constraints' => [
                    'param1' => 'A-Za-z',
                ],
                'defaults' => [
                    'param1' => 'index',
                ],
            ],
        ]);
    }
}
