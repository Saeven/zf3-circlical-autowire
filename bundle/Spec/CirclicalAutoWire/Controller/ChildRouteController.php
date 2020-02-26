<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class ChildRouteController
 * @package Spec\CirclicalAutoWire\Controller
 */
class ChildRouteController extends AbstractActionController
{

    /**
     * @Route("/icecream", name="icecream", terminate=true)
     */
    public function indexAction()
    {
    }

    /**
     * This is a sample docblock
     *
     * @Route("/eat", parent="icecream", name="eat")
     */
    public function eatAction()
    {
    }


    /**
     * @Route("/select/:flavor", constraints={"flavor":"\d"}, name="select", parent="icecream")
     */
    public function selectFlavorAction()
    {
    }

}
