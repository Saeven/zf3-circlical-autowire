<?php

namespace CirclicalAutoWire\Model;

use CirclicalAutoWire\Annotations\Route;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

final class AnnotatedRoute
{
    private $route;

    private $controller;

    private $action;

    private $children;

    public function getParent()
    {
        return $this->route->parent;
    }

    public function addChild(string $routeName, AnnotatedRoute $route)
    {
        if (!$this->children) {
            $this->children = [];
        }
        $this->children[$routeName] = $route;
    }

    public function getChild(string $routeName)
    {
        return $this->children[$routeName] ?? null;
    }

    public function __construct(Route $route, string $controller, string $action)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function toArray(): array
    {
        $route = [
            'type' => $this->type ?? $this->route->type ?? $this->identifyRoute($this->route->value),
            'options' => [
                'route' => $this->route->value,
                'defaults' => [
                    'controller' => $this->controller,
                    'action' => preg_replace('/Action$/', '', $this->action),
                ],
            ],
        ];

        if ($this->route->constraints) {
            $route['options']['constraints'] = $this->route->constraints;
        }

        if ($this->route->defaults) {
            $route['options']['defaults'] = $this->route->defaults;
        }

        if ($this->children) {
            if ($this->route->terminate) {
                $route['may_terminate'] = $this->route->terminate;
            }

            $childRoutes = [];
            foreach ($this->children as $routeName => $annotatedRoute) {
                $childRoutes[$routeName] = $annotatedRoute->toArray();
            }
            $route['child_routes'] = $childRoutes;
        }

        return $route;
    }

    private function identifyRoute(string $route): string
    {
        if (strpos($route, ':') !== false || strpos($route, '[') !== false) {
            return Segment::class;
        }

        return Literal::class;
    }
}
