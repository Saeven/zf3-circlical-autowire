<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class SimpleController
 * @package Spec\CirclicalAutoWire\Controller
 * @Route("/admin/config")
 */
class AnnotatedController extends AbstractActionController
{
    /**
     * This is a sample docblock
     *
     * @Route("/foobar", name="baseroute1")
     */
    public function fooAction()
    {
    }


    /**
     * @Route("/foo/:param1/:param2", constraints={"param1":"\d","param2":"A-Za-z"}, name="baseroute2")
     */
    public function routeParamAction()
    {
    }

}
