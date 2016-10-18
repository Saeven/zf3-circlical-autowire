<?php

return [

    'circlical' => [

        'autowire' => [

            /**
             * If the system is not in production mode, it will 'drop' a route config into this file.  Please provide full working path.
             */

            'compile_to' => getcwd() . '/config/autowire.routes.php',


            /**
             * In production mode, the routes will not be scanned from annotations. Instead, your route config will be merged with
             * any routes that were previously compiled by the system.  You'd probably want to set this to something like
             *
             *     getenv('APPLICATION_ENVIRONMENT') == 'production'
             *
             */

            'production_mode' => false,
        ],
    ],

    'service_manager' => [
        'factories' => [
            \CirclicalAutoWire\Service\RouterService::class => \CirclicalAutoWire\Factory\Service\RouterServiceFactory::class,
        ],
    ],

    'controllers' => [
        'abstract_factories' => [
            \CirclicalAutoWire\Factory\Controller\ReflectionFactory::class,
        ],
    ],

];
