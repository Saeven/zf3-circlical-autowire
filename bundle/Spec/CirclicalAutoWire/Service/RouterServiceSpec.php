<?php

namespace Spec\CirclicalAutoWire\Service;

use Spec\CirclicalAutoWire\Controller\SimpleController;
use CirclicalAutoWire\Service\RouterService;
use PhpSpec\ObjectBehavior;
use Zend\Router\Http\TreeRouteStack;

class RouterServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouterService::class);
    }

    function let(TreeRouteStack $routeStack)
    {
        $this->beConstructedWith($routeStack);
    }

    function it_parses_controllers_by_class()
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/SimpleController.php';
        $this->parseController(SimpleController::class);
    }
}
