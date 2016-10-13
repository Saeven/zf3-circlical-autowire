<?php

namespace CirclicalAutoWire\Annotations;

/**
 * @Annotation
 * @Target("METHOD")
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
    public $type;

}