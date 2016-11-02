<?php

namespace Spec\CirclicalAutoWire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class SameNameBController
 * @package Spec\CirclicalAutoWire\Controller
 */
class SameNameBController extends AbstractActionController
{
    /**
     * This is a sample docblock
     *
     * @Route("/bar", name="barcrud")
     */
    public function crudAction()
    {
    }


    /**
     * @Route("/add", name="add", parent="barcrud")
     */
    public function addAction()
    {
    }

}
