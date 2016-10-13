<?php

namespace Spec\CirclicalAutoWire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
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

}
