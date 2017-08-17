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
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
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
            $controllerClasses = [];

            /** @var ModuleManager $moduleManager */
            $moduleManager = $application->getServiceManager()->get('ModuleManager');
            foreach ($moduleManager->getLoadedModules() as $module) {
                // ignore all Zend modules
                if (strpos(get_class($module), 'Zend\\') === 0) {
                    continue;
                }

                $reflector = new \ReflectionClass($module);
                $moduleSrcPath = dirname($reflector->getFileName());
                if (is_dir($moduleSrcPath)) {
                    $directoryScanner = new DirectoryScanner($moduleSrcPath);
                    foreach ($directoryScanner->getClassNames() as $className) {
                        if (false !== strpos($className, '\\Controller\\') && class_exists($className)) {
                            $controllerClasses[] = $className;
                        }
                    }
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
