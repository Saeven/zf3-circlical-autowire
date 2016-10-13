<?php

namespace CirclicalAutoWire\Factory\Listener;


use CirclicalAutoWire\Listener\ModuleLoadedListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ModuleLoadedListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ModuleLoadedListener();
    }
}