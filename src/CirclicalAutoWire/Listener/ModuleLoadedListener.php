<?php


namespace CirclicalAutoWire;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\ModuleEvent;

class ModuleLoadedListener implements ListenerAggregateInterface
{

    protected $listeners;

    public function __construct()
    {

    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES, [$this, 'scanModuleControllers']);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    private function scanModuleControllers(ModuleEvent $moduleEvent)
    {
        $module = $moduleEvent->getModule();
        if (!$module instanceof AutoloaderProviderInterface
            && !method_exists($module, 'getAutoloaderConfig')
        ) {
            return;
        }
        $autoloaderConfig = $module->getAutoloaderConfig();
        var_dump($autoloaderConfig);
    }
}

