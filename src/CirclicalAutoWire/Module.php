<?php

namespace CirclicalAutoWire;

use CirclicalAutoWire\Service\RouterService;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;
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

        $config = $serviceLocator->get('config');
        $productionMode = $config['circlical']['autowire']['production_mode'];

        if (!$productionMode) {
            $routerService = $serviceLocator->get(RouterService::class);
            $directoryScanner = new DirectoryScanner();

            foreach ($this->modulesToScan as $moduleName) {
                if (is_dir(getcwd() . '/module/' . $moduleName)) {
                    $directoryScanner->addDirectory(getcwd() . '/module/' . $moduleName . '/src/');
                }
            }

            $controllerClasses = [];
            foreach ($directoryScanner->getClassNames() as $className) {
                if (strstr($className, '\\Controller\\')) {
                    $controllerClasses[] = $className;
                }
            }

            $routeConfig = [];
            foreach ($controllerClasses as $controllerClass) {
                $routeConfig += $routerService->parseController($controllerClass);
            }

            $routeConfig = new Config($routeConfig, false);
            $writer = new PhpArray();
            $writer->toFile($config['circlical']['autowire']['compile_to'], $routeConfig, true);

        }
    }


}