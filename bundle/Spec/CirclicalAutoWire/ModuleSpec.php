<?php

namespace Spec\CirclicalAutoWire;

use CirclicalAutoWire\Module;
use CirclicalAutoWire\Service\RouterService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Zend\Console\Console;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Listener\ConfigListener;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\TreeRouteStack;

class ModuleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Module::class);
    }

    function it_returns_its_config()
    {
        $this->getConfig()->shouldBeArray();
    }

    function it_binds_its_events(ModuleManager $moduleManager, EventManager $eventManager)
    {
        $eventManager->attach(ModuleEvent::EVENT_LOAD_MODULE, Argument::type('array'))->shouldBeCalled();
        $eventManager->attach(ModuleEvent::EVENT_MERGE_CONFIG, Argument::type('array'))->shouldBeCalled();
        $moduleManager->getEventManager()->willReturn($eventManager);
        $this->init($moduleManager);
    }

    function it_merges_config(ModuleEvent $event, ConfigListener $configListener)
    {
        $event->getConfigListener()->willReturn($configListener);
        $configListener->getMergedConfig(false)->willReturn(include __DIR__ . '/../../../config/module.config.php');
        $this->configMerge($event);
    }

    function it_freaks_out_if_autowire_config_is_missing(ModuleEvent $event, ConfigListener $configListener)
    {
        $event->getConfigListener()->willReturn($configListener);
        $config = include __DIR__ . '/../../../config/module.config.php';
        unset($config['circlical']);
        $configListener->getMergedConfig(false)->willReturn($config);
        $this->shouldThrow(\Exception::class)->during('configMerge', [$event]);
    }

    function it_sets_routes_in_production_mode_during_config_merge(ModuleEvent $event, ConfigListener $configListener)
    {
        $event->getConfigListener()->willReturn($configListener);
        $config = include __DIR__ . '/../../../config/module.config.php';
        $config['circlical']['autowire']['compile_to'] = __DIR__ . '/compiled_routes.php';
        $configListener->getMergedConfig(false)->willReturn($config);
        $configListener->setMergedConfig(Argument::type('array'))->shouldBeCalled();
        $this->configMerge($event);
    }

    function it_listens_for_loading_modules(ModuleEvent $event1, ModuleEvent $event2)
    {
        $event1->getModuleName()->willReturn('A');
        $event2->getModuleName()->willReturn('B');
        $this->moduleLoaded($event1);
        $this->moduleLoaded($event2);
    }

    function it_merges_with_existing_routes_in_production_mode_during_config_merge(ModuleEvent $event, ConfigListener $configListener)
    {
        $event->getConfigListener()->willReturn($configListener);
        $config = include __DIR__ . '/../../../config/module.config.php';
        $config['circlical']['autowire']['compile_to'] = __DIR__ . '/compiled_routes.php';
        $config['router']['routes'] = [];
        $configListener->getMergedConfig(false)->willReturn($config);
        $configListener->setMergedConfig(Argument::type('array'))->shouldBeCalled();
        $this->configMerge($event);
    }

    function it_scans_modules_during_bootstrap_in_dev_mode_only(MvcEvent $mvcEvent, Application $application, ContainerInterface $container)
    {
        $mvcEvent->getApplication()->willReturn($application);
        $application->getServiceManager()->willReturn($container);

        Console::overrideIsConsole(false);

        $container->get('config')->willReturn([
            'circlical' => [
                'autowire' => [
                    'production_mode' => false,
                    'compile_to' =>  __DIR__ . '/compiled_routes.php',
                ],
            ],
        ]);

        $container->get(RouterService::class)->willReturn(new RouterService(new TreeRouteStack(), false));

        $this->onBootstrap($mvcEvent);
    }

    function it_skips_compiling_in_prod_mode(MvcEvent $mvcEvent, Application $application, ContainerInterface $container)
    {
        $mvcEvent->getApplication()->willReturn($application);
        $application->getServiceManager()->willReturn($container);

        Console::overrideIsConsole(false);

        $container->get('config')->willReturn([
            'circlical' => [
                'autowire' => [
                    'production_mode' => true,
                    'compile_to' =>  __DIR__ . '/compiled_routes.php',
                ],
            ],
        ]);

        $container->get(RouterService::class)->shouldNotBeCalled();

        $this->onBootstrap($mvcEvent);
    }
}
