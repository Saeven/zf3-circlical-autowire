<?php

namespace CirclicalAutoWire;

use CirclicalAutoWire\Listener\ModuleLoadedListener;
use Zend\Mvc\MvcEvent;
use Zend\Console\Console;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $mvcEvent)
    {
        if (Cons::isConsole()) {
            return;
        }

        $application = $mvcEvent->getApplication();
        $serviceLocator = $application->getServiceManager();
        $strategy = $serviceLocator->get(ModuleLoadedListener::class);
        $eventManager = $application->getEventManager();
        $strategy->attach($eventManager);
    }

}