<?php

declare(strict_types=1);

namespace CirclicalAutoWire\Model;

use CirclicalAutoWire\Annotations\Route;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

use function preg_replace;
use function strpos;

final class AnnotatedRoute
{
    private Route $route;

    private string $controller;
    private string $action;
    private ?array $children = null;

    public function __construct(Route $route, string $controller, string $action)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getParent(): ?string
    {
        return $this->route->parent;
    }

    public function addChild(string $routeName, AnnotatedRoute $route): void
    {
        if (!$this->children) {
            $this->children = [];
        }
        $this->children[$routeName] = $route;
    }

    public function getChild(string $routeName): ?string
    {
        return $this->children[$routeName] ?? null;
    }

    public function toArray(): array
    {
        $route = [
            'type' => $this->route->type ?? $this->identifyRoute($this->route->value),
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

        if ($this->route->priority) {
            $route['priority'] = $this->route->priority;
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
