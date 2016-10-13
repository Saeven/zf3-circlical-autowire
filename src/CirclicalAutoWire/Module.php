<?php

namespace CirclicalAutoWire;

use CirclicalAutoWire\Service\RouterService;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Console\Console;

class Module
{
    private $modulesToScan = [];

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, [$this, 'moduleLoaded']);
    }

    public function moduleLoaded(ModuleEvent $event)
    {
        $this->modulesToScan[] = $event->getModuleName();
    }

    public function onBootstrap(MvcEvent $mvcEvent)
    {
        if (Console::isConsole()) {
            return;
        }

        /** @var RouterService $routerService */
        $application = $mvcEvent->getApplication();
        $serviceLocator = $application->getServiceManager();
        $routerService = $serviceLocator->get(RouterService::class);
        $sc = new DirectoryScanner();

        foreach ($this->modulesToScan as $moduleName) {
            if (is_dir(getcwd() . '/module/' . $moduleName)) {
                $sc->addDirectory(getcwd() . '/module/' . $moduleName . '/src/');
            }
        }

        $controllerClasses = [];
        foreach ($sc->getClassNames() as $className) {
            if (strstr($className, '\\Controller\\')) {
                $controllerClasses[] = $className;
            }
        }

        foreach ($controllerClasses as $controllerClass) {
            $routerService->parseController($controllerClass);
        }
    }


}