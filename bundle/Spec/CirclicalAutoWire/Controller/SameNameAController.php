<?php

namespace Spec\CirclicalAutoWire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class SameNameAController
 * @package Spec\CirclicalAutoWire\Controller
 */
class SameNameAController extends AbstractActionController
{
    /**
     * This is a sample docblock
     *
     * @Route("/foo", name="foocrud")
     */
    public function crudAction()
    {
    }


    /**
     * @Route("/add", name="add", parent="foocrud")
     */
    public function addAction()
    {
    }

}
