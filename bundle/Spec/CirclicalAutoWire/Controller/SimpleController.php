<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class SimpleController
 * @package Spec\CirclicalAutoWire\Controller
 */
class SimpleController extends AbstractActionController
{
    /**
     * This is a sample docblock
     *
     * @Route("/foobar")
     */
    public function fooAction()
    {
    }


    /**
     * @Route("/foo/:param1/:param2", constraints={"param1":"\d","param2":"A-Za-z"})
     */
    public function routeParamAction()
    {
    }

}
