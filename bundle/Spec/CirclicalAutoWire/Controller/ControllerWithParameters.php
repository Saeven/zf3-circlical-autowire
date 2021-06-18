<?php

namespace Spec\CirclicalAutoWire\Controller;

use Spec\CirclicalAutoWire\Form\DummyForm;
use Spec\CirclicalAutoWire\Model\DummyObject;
use Laminas\EventManager\EventManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Form\FormElementManager;

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
     * @param FormElementManager           $formManager
     * @param DummyForm                    $form               This should invoke the FormElementManager
     * @param array                        $config             This is a magic name that conjures the ZF config
     * @param                              $formElementManager Magic parameter that injects the FormElementManager
     * @param EventManager                 $eventManager       Should inject an application event manager, lazy shorthand
     */
    public function __construct(
        DummyObject $dummyObject,
        FormElementManager $formManager,
        DummyForm $form,
        array $config,
        FormElementManager $formElementManager,
        EventManager $eventManager
    ) {
    }

}
