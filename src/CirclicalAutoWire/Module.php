<?php

declare(strict_types=1);

namespace CirclicalAutoWire;

use CirclicalAutoWire\Service\RouterService;
use Exception;
use Laminas\Config\Config;
use Laminas\Config\Writer\PhpArray;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

use function array_merge;
use function getcwd;
use function is_dir;
use function is_file;
use function strpos;

use const PHP_SAPI;

class Module
{
    protected static ?bool $isConsole = null;
    private array $modulesToScan = [];

    public function getConfig(): array
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function init(ModuleManager $moduleManager): void
    {
        $events = $moduleManager->getEventManager();
        $events->attach(ModuleEvent::EVENT_LOAD_MODULE, [$this, 'moduleLoaded']);
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'configMerge']);
    }

    public function moduleLoaded(ModuleEvent $event): void
    {
        $this->modulesToScan[] = $event->getModuleName();
    }

    public function configMerge(ModuleEvent $e): void
    {
        $configListener = $e->getConfigListener();
        $configuration = $configListener->getMergedConfig(false);

        if (!isset($configuration['circlical']['autowire'])) {
            throw new Exception("Autowire module enabled, but the config wasn't available!");
        }

        if (static::isConsole() || $configuration['circlical']['autowire']['production_mode']) {
            if (is_file($configuration['circlical']['autowire']['compile_to'])) {
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

    public static function isConsole(): bool
    {
        if (null === static::$isConsole) {
            static::$isConsole = PHP_SAPI === 'cli';
        }

        return static::$isConsole;
    }

    public static function overrideIsConsole(?bool $flag): void
    {
        static::$isConsole = $flag;
    }

    public function scanForControllers(array $rootDirectoriesToScan): array
    {
        $astLocator = (new BetterReflection())->astLocator();
        $sourceLocator = new DirectoriesSourceLocator($rootDirectoriesToScan, $astLocator);
        $reflector = new DefaultReflector($sourceLocator);

        $controllerClasses = [];
        foreach ($reflector->reflectAllClasses() as $reflection) {
            $className = $reflection->getName();
            if (false !== strpos($className, '\\Controller\\')) {
                $controllerClasses[] = $className;
            }
        }

        return $controllerClasses;
    }

    public function onBootstrap(MvcEvent $mvcEvent)
    {
        $application = $mvcEvent->getApplication();
        $serviceLocator = $application->getServiceManager();

        $config = $serviceLocator->get('config');
        $productionMode = static::isConsole() || $config['circlical']['autowire']['production_mode'];

        if (!$productionMode) {
            $routerService = $serviceLocator->get(RouterService::class);
            $rootDirectoriesToScan = [];

            foreach ($this->modulesToScan as $moduleName) {
                if (is_dir(getcwd() . '/module/' . $moduleName)) {
                    $rootDirectoriesToScan[] = getcwd() . '/module/' . $moduleName . '/src/';
                }
            }

            $controllerClasses = $this->scanForControllers($rootDirectoriesToScan);

            foreach ($controllerClasses as $controllerClass) {
                $routerService->parseController($controllerClass);
            }

            $routeConfig = new Config($routerService->compile(), false);
            $writer = new PhpArray();
            $writer->toFile($config['circlical']['autowire']['compile_to'], $routeConfig, true);
            $routerService->reset();
        }
    }
}
