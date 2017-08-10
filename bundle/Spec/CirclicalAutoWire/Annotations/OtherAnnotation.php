<?php

namespace Spec\CirclicalAutoWire\Annotations;

/**
 * Class OtherAnnotation
 *
 * @package Spec\CirclicalAutoWire\Annotations
 * @author Michał Makaruk <buliq1847@gmail.com>
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class OtherAnnotation
{
    /**
     * @var string
     */
    public $value;

}
