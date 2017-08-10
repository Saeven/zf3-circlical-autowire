<?php

namespace Spec\CirclicalAutoWire\Controller;

use Spec\CirclicalAutoWire\Form\DummyForm;
use Spec\CirclicalAutoWire\Model\DummyObject;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class ControllerWithParameters
 * @package Spec\CirclicalAutoWire\Controller
 */
class ControllerWithParameters extends AbstractActionController
{
    /**
     * ControllerWithParameters constructor.
     *
     * @param DummyObject                  $dummyObject
     * @param FormElementManagerV3Polyfill $formManager
     * @param DummyForm                    $form   This should invoke the FormElementManager
     * @param array                        $config This is a magic name that conjures the ZF config
     * @param                              $formElementManager Magic parameter that injects the FormElementManager
     * @param                              $serviceLocator Should inject the container (bad, but here by popular demand)
     */
    public function __construct(DummyObject $dummyObject, FormElementManagerV3Polyfill $formManager, DummyForm $form, array $config, $formElementManager, $serviceLocator)
    {

    }

}
