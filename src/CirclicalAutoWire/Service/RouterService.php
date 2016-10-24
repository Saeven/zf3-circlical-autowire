<?php

namespace CirclicalAutoWire\Service;

use CirclicalAutoWire\Annotations\Route;
use CirclicalAutoWire\Model\AnnotatedRoute;
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

    private $productionMode;

    public function __construct(TreeRouteStack $router, bool $productionMode)
    {
        AnnotationRegistry::registerAutoloadNamespace("CirclicalAutoWire\\Annotations", realpath(__DIR__ . "/../../"));
        $this->router = $router;
        $this->reader = new AnnotationReader();
        $this->productionMode = $productionMode;
    }

    public function parseController(string $controllerClass): array
    {
        $class = new \ReflectionClass($controllerClass);
        $classAnnotation = $this->reader->getClassAnnotation($class, Route::class);
        $annotations = [];
        $routes = [];

        // First, get all annotations for this controller

        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() == $controllerClass) {
                $set = $this->reader->getMethodAnnotations($method, Route::class);
                /** @var Route $routerAnnotation */
                foreach ($set as $routerAnnotation) {
                    if ($classAnnotation) {
                        $routerAnnotation->setPrefix($classAnnotation->value);
                    }
                    $routeName = $routerAnnotation->name ?? 'route-' . static::$routesParsed++;
                    $annotations[$routeName] = new AnnotatedRoute($routerAnnotation, $controllerClass, $method->getName());
                }
            }
        }

        // Then, associate children to parents
        foreach ($annotations as $routeName => $annotatedRoute) {
            if( $parent = $annotatedRoute->getParent() ){
                if( !isset($annotations[$parent]) ){
                    throw new \Exception( "An autowired route $routeName declares a parent of $parent, but $parent was not defined.");
                }
                $annotations[$parent]->addChild( $routeName, $annotatedRoute );
            }
        }

        // Lastly, push all stacked routes into the router
        foreach( $annotations as $routeName => $annotatedRoute ){
            if( $annotatedRoute->getParent() ){
                continue;
            }
            $routeParams = $annotatedRoute->toArray();
            $this->router->addRoute($routeName,$routeParams);
            $routes[] = $routeParams;
        }

        return $routes;
    }
}