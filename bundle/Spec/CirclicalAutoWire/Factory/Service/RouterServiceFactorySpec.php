<?php

namespace Spec\CirclicalAutoWire\Factory\Service;

use CirclicalAutoWire\Factory\Service\RouterServiceFactory;
use CirclicalAutoWire\Service\RouterService;
use Interop\Container\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Zend\Router\Http\TreeRouteStack;

class RouterServiceFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouterServiceFactory::class);
    }

    function it_creates_router_services(ContainerInterface $container, TreeRouteStack $stack)
    {
        $container->get('config')->willReturn([
            'circlical' => [
                'autowire' => [
                    'production_mode' => false,
                ],
            ],
        ]);
        $container->get('HttpRouter')->willReturn($stack);

        $this->__invoke($container, RouterService::class)->shouldBeAnInstanceOf(RouterService::class);
    }
}
