<?php

return [

    'service_manager' => [
        'factories' => [
            \CirclicalAutoWire\Service\RouterService::class => \CirclicalAutoWire\Factory\Service\RouterServiceFactory::class,
        ],
    ],

];
