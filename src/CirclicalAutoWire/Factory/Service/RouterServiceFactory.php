<?php

declare(strict_types=1);

namespace CirclicalAutoWire\Factory\Service;

use CirclicalAutoWire\Service\RouterService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class RouterServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new RouterService($container->get('HttpRouter'));
    }
}
