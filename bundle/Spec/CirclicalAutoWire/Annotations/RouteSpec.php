<?php

namespace Spec\CirclicalAutoWire\Annotations;

use CirclicalAutoWire\Annotations\Route;
use PhpSpec\ObjectBehavior;

class RouteSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Route::class);
    }
}
