<?php

namespace CirclicalAutoWire\Annotations;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class Route
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

    /**
     * @var bool
     */
    public $terminate;

    /**
     * @var string
     */
    public $parent;

    /**
     * @var int
     */
    public $priority;


    public function setPrefix($path)
    {
        $this->value = str_replace('//', '/', $path . $this->value);
    }

}