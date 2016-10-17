<?php

namespace CirclicalAutoWire\Service;

use CirclicalAutoWire\Annotations\Route;
use Doctrine\Common\Annotations\AnnotationReader;
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

    public static $routesParsed = 0;

    public function __construct(TreeRouteStack $router)
    {
        AnnotationRegistry::registerAutoloadNamespace("CirclicalAutoWire\\Annotations", realpath(__DIR__ . "/../../"));
        $this->router = $router;
        $this->reader = new AnnotationReader();
    }

    public function parseController(string $controllerClass)
    {
        $class = new \ReflectionClass($controllerClass);
        $classAnnotation = $this->reader->getClassAnnotation($class, Route::class);


        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() == $controllerClass) {

                $set = $this->reader->getMethodAnnotations($method, Route::class);

                /** @var Route $routerAnnotation */
                foreach ($set as $routerAnnotation) {
                    if( $classAnnotation ){
                        $routerAnnotation->setPrefix( $classAnnotation->value );
                    }
                    $this->router->addRoute(
                        $routerAnnotation->name ?? 'route-' . static::$routesParsed++,
                        $routerAnnotation->transform($controllerClass, $method->getName())
                    );
                }
            }
        }
    }


}