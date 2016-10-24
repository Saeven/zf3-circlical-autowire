<?php

namespace Spec\CirclicalAutoWire\Model;

use CirclicalAutoWire\Annotations\Route;
use CirclicalAutoWire\Model\AnnotatedRoute;
use PhpSpec\ObjectBehavior;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class AnnotatedRouteSpec extends ObjectBehavior
{
    const DUMMY_CONTROLLER = 'C';
    const DUMMY_METHOD = 'M';

    function it_is_initializable()
    {
        $this->beConstructedWith(new Route(), self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $this->shouldHaveType(AnnotatedRoute::class);
    }


    function it_processes_literal_routes()
    {
        $route = new Route();
        $route->value = "/foo";

        $this->beConstructedWith($route, self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig = $this->toArray();
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
        $route = new Route();
        $route->value = "/foo/:param1";
        $this->beConstructedWith($route, self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig = $this->toArray();

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
        $route = new Route();
        $route->value = "/foo/:param1";
        $route->constraints = ['param1' => "A-Za-z"];
        $this->beConstructedWith($route, self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig = $this->toArray();
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
        $route = new Route();
        $route->value = "/foo/:param1";
        $route->constraints = ['param1' => "A-Za-z"];
        $route->defaults = ['param1' => 'index'];
        $this->beConstructedWith($route, self::DUMMY_CONTROLLER, self::DUMMY_METHOD);
        $routeConfig = $this->toArray();
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
