<?php

namespace Spec\CirclicalAutoWire\Annotations;

use CirclicalAutoWire\Annotations\Route;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

class RouteSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Route::class);
    }
}
