<?php

declare(strict_types=1);

namespace CirclicalAutoWire\Service;

use CirclicalAutoWire\Annotations\Route;
use CirclicalAutoWire\Model\AnnotatedRoute;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Exception;
use Laminas\Router\Http\TreeRouteStack;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use function array_shift;
use function count;
use function dirname;
use function end;
use function explode;
use function ksort;
use function str_pad;
use function strlen;
use function strpos;
use function uasort;

use const STR_PAD_LEFT;

/**
 * This service's purpose, is to bridge annotations with the router.
 */
final class RouterService
{
    public static int $routesParsed = 0;
    private TreeRouteStack $router;
    private AnnotationReader $reader;
    private array $annotations;

    public function __construct(TreeRouteStack $router)
    {
        AnnotationRegistry::registerAutoloadNamespace("CirclicalAutoWire\\Annotations", dirname(__DIR__, 2) . '/');
        $this->router = $router;
        $this->reader = new AnnotationReader();
        $this->annotations = [];
    }

    /**
     * Reset the annotations variable
     */
    public function reset()
    {
        $this->annotations = [];
    }

    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * Parse a controller, storing results into the 'annotations' class variable
     *
     * @throws ReflectionException
     */
    public function parseController(string $controllerClass)
    {
        $class = new ReflectionClass($controllerClass);
        /** @var Route $classAnnotation */
        $classAnnotation = $this->reader->getClassAnnotation($class, Route::class);

        // First, get all annotations for this controller

        /** @var ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() === $controllerClass) {
                $set = $this->reader->getMethodAnnotations($method);
                /** @var Route $routerAnnotation */
                foreach ($set as $routerAnnotation) {
                    if (!$routerAnnotation instanceof Route) {
                        continue;
                    }

                    if ($classAnnotation) {
                        $routerAnnotation->setPrefix($classAnnotation->value);
                    }

                    $routeName = $routerAnnotation->name ?? ('route-' . str_pad((string) static::$routesParsed++, 5, '0', STR_PAD_LEFT));
                    if ($routerAnnotation->parent) {
                        $routeName = $routerAnnotation->parent . '/' . $routeName;
                    }
                    $this->annotations[$routeName] = new AnnotatedRoute($routerAnnotation, $controllerClass, $method->getName());
                }
            }
        }
    }

    /**
     * Compile routes into an array, simultaneously adding them to the router
     *
     * @throws Exception
     */
    public function compile(): array
    {
        ksort($this->annotations);

        /** @var AnnotatedRoute[] $routes */
        $routes = [];
        foreach ($this->annotations as $routeName => $annotatedRoute) {
            if (strpos($routeName, '/') === false) {
                $routes[$routeName] = $annotatedRoute;
            } else {
                $routePath = explode('/', $routeName);
                $baseRouteName = array_shift($routePath);
                if (!isset($routes[$baseRouteName])) {
                    throw new Exception("An autowired route declares a parent of $baseRouteName, but $baseRouteName is not defined.");
                }

                $parentRoute = $routes[$baseRouteName];
                for ($i = 0; $i < count($routePath) - 1; $i++) {
                    $parentRoute = $parentRoute->getChild($routePath[$i]);
                }
                $parentRoute->addChild(end($routePath), $annotatedRoute);
            }
        }

        // Lastly, push all stacked routes into the router
        $routeConfig = [];
        foreach ($routes as $routeName => $annotatedRoute) {
            $routeParams = $annotatedRoute->toArray();
            $this->router->addRoute($routeName, $routeParams);
            $routeConfig[$routeName] = $routeParams;
        }

        // Sort them Segment first, Literal last (for LIFO) and by length
        uasort($routeConfig, static function (array $a, array $b) {
            if ($a['type'] === $b['type']) {
                return strlen($a['options']['route']) - strlen($b['options']['route']);
            }

            return -1 * ($a['type'] <=> $b['type']);
        });

        return $routeConfig;
    }
}
