<?php

namespace Spec\CirclicalAutoWire\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use CirclicalAutoWire\Annotations\Route;

/**
 * Class DistantChildRouteController
 * @package Spec\CirclicalAutoWire\Controller
 */
class DistantChildRouteController extends AbstractActionController
{
    /**
     * This is a sample docblock
     *
     * @Route("/melt", parent="icecream", name="melt")
     */
    public function meltAction()
    {
    }
}
