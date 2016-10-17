<?php

namespace CirclicalAutoWire\Annotations;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Route
{
    /**
     * @Required
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;


    /**
     * @var array
     */
    public $constraints;


    /**
     * @var array
     */
    public $defaults;


    public function transform(string $controllerClass, string $methodName): array
    {
        $route = [
            'type' => $this->type ?? $this->identifyRoute($this->value),
            'options' => [
                'route' => $this->value,
                'defaults' => [
                    'controller' => $controllerClass,
                    'action' => preg_replace('/Action$/', '', $methodName),
                ],
            ],
        ];

        if ($this->constraints) {
            $route['options']['constraints'] = $this->constraints;
        }

        if ($this->defaults) {
            $route['options']['defaults'] = $this->defaults;
        }

        return $route;
    }

    private function identifyRoute(string $route): string
    {
        if (strpos($route, ':') !== false) {
            return Segment::class;
        }

        return Literal::class;
    }

    public function setPrefix($path)
    {
        $this->value = str_replace('//', '/', $path . $this->value);
    }

}