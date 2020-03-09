<?php

namespace CirclicalAutoWire;

use CirclicalAutoWire\Service\RouterService;
use Laminas\Code\Scanner\DirectoryScanner;
use Laminas\Config\Config;
use Laminas\Config\Writer\PhpArray;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Console\Console;

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
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'configMerge']);
    }

    public function moduleLoaded(ModuleEvent $event)
    {
        $this->modulesToScan[] = $event->getModuleName();
    }

    public function configMerge(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $configuration = $configListener->getMergedConfig(false);

        if (!isset($configuration['circlical']['autowire'])) {
            throw new \Exception("Autowire module enabled, but the config wasn't available!");
        }

        if (Console::isConsole() || $configuration['circlical']['autowire']['production_mode']) {
            if (file_exists($configuration['circlical']['autowire']['compile_to'])) {
                $autowiredRoutes = include $configuration['circlical']['autowire']['compile_to'];
                if (isset($configuration['router']['routes'])) {
                    $configuration['router']['routes'] = array_merge($configuration['router']['routes'], $autowiredRoutes);
                } else {
                    $configuration['router']['routes'] = $autowiredRoutes;
                }
                $configListener->setMergedConfig($configuration);
            }
        }
    }

    public function onBootstrap(MvcEvent $mvcEvent)
    {
        /** @var RouterService $routerService */
        $application = $mvcEvent->getApplication();
        $serviceLocator = $application->getServiceManager();

        $config = $serviceLocator->get('config');
        $productionMode = Console::isConsole() || $config['circlical']['autowire']['production_mode'];

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
                if (false !== strpos($className, '\\Controller\\')) {
                    $controllerClasses[] = $className;
                }
            }

            foreach ($controllerClasses as $controllerClass) {
                $routerService->parseController($controllerClass);
            }

            $routeConfig = new Config($routerService->compile(), false);
            $writer = new PhpArray();
            $writer->toFile($config['circlical']['autowire']['compile_to'], $routeConfig, true);
            $routerService->reset();

            $configListener = $serviceLocator->get(ModuleManager::class)->getEvent()->getConfigListener();
            if ($productionMode && $configListener->getOptions()->getConfigCacheEnabled() && file_exists($configListener->getOptions()->getConfigCacheFile())) {
                @unlink($configListener->getOptions()->getConfigCacheFile());
            }
        }
    }
}
