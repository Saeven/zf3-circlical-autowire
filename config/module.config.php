<?php

return [

    'service_manager' => [
        'factories' => [
            \CirclicalAutoWire\Listener\ModuleLoadedListener::class => \CirclicalAutoWire\Factory\Listener\ModuleLoadedListenerFactory::class,
        ],
    ],

];
