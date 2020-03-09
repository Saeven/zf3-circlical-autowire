<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;
use Spec\CirclicalAutoWire\Annotations\OtherAnnotation;

/**
 * Class OtherAnnotationsTypeErrorController
 * @package Spec\CirclicalAutoWire\Controller
 *
 */
class OtherAnnotationsTypeErrorController extends AbstractActionController
{
    /**
     * @OtherAnnotation
     * @Route("/will-it-fail")
     */
    public function willItFailAction()
    {
    }

}
