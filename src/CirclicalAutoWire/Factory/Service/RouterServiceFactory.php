<?php

namespace CirclicalAutoWire\Factory\Service;


use CirclicalAutoWire\Service\RouterService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class RouterServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        return new RouterService($container->get('HttpRouter'), $config['circlical']['autowire']['production_mode']);
    }
}