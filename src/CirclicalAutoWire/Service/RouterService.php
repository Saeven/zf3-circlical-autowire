<?php

namespace CirclicalAutoWire\Service;

use CirclicalAutoWire\Annotations\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Router\Http\Literal;
use Zend\Router\Http\TreeRouteStack;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class RouterService
 * @package CirclicalAutoWire\Service
 *
 * This service's purpose, is to bridge annotations with the Zend SM router
 */
class RouterService
{
    private $router;

    private $reader;

    static $routesParsed = 0;

    public function __construct(TreeRouteStack $router)
    {
        AnnotationRegistry::registerAutoloadNamespace("CirclicalAutoWire\\Annotations", realpath(__DIR__ . "/../../"));
        $this->router = $router;
        $this->reader = new AnnotationReader();
    }

    public function parseController(string $controllerClass)
    {
        $class = new \ReflectionClass($controllerClass);

        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() == $controllerClass) {

                $set = $this->reader->getMethodAnnotations($method, 'Route');

                /** @var Route $routerAnnotation */
                foreach ($set as $routerAnnotation) {
                    $this->router->addRoute(
                        $routerAnnotation->name ?? 'route-' . static::$routesParsed++,
                        [
                            'type' => $routerAnnotation->type ?? Literal::class,
                            'options' => [
                                'route' => $routerAnnotation->value,
                                'defaults' => [
                                    'controler' => $controllerClass,
                                    'action' => preg_replace('/Action$/', '', $method->getName()),
                                ],
                            ],
                        ]
                    );
                }
            }
        }
    }


}