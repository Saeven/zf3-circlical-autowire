<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;
use Spec\CirclicalAutoWire\Annotations\OtherAnnotation;

/**
 * Class OtherAnnotationsTypeErrorController
 * @package Spec\CirclicalAutoWire\Controller
 *
 * @Route("/foobar")
 */
class OtherAnnotationsController extends AbstractActionController
{
    /**
     * @Route("/")
     */
    public function fooAction()
    {
    }

    /**
     * @OtherAnnotation
     * @Route("/ok")
     */
    public function willItFailAction()
    {
    }

}
