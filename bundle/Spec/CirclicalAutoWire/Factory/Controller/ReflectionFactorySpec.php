<?php

namespace Spec\CirclicalAutoWire\Factory\Controller;

use CirclicalAutoWire\Factory\Controller\ReflectionFactory;
use Interop\Container\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Spec\CirclicalAutoWire\Controller\AnnotatedController;
use Spec\CirclicalAutoWire\Controller\ControllerWithParameters;
use Spec\CirclicalAutoWire\Form\DummyForm;
use Spec\CirclicalAutoWire\Model\DummyObject;
use Laminas\EventManager\EventManager;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill;
use Laminas\Mvc\Application;

class ReflectionFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReflectionFactory::class);
    }

    function it_can_create_classes_that_end_with_controller(ContainerInterface $interface)
    {
        $this->canCreate($interface, 'SuperController')->shouldBe(true);
    }

    function it_only_creates_controllers(ContainerInterface $interface)
    {
        $this->canCreate($interface, 'SuperFactory')->shouldBe(false);
    }

    function it_can_create_controllers_through_reflection(ContainerInterface $interface, DummyObject $dummyObject, FormElementManagerV3Polyfill $formManager, DummyForm $form, EventManager $eventManager, Application $application)
    {
        $formManager->get(DummyForm::class)->willReturn($form);
        $interface->get(DummyObject::class)->willReturn($dummyObject);
        $interface->get('FormElementManager')->willReturn($formManager);

        $application->getEventManager()->willReturn($eventManager);
        $interface->get('Application')->willReturn($application);

        $interface->get('config')->willReturn([]);
        $this->__invoke($interface, ControllerWithParameters::class);

        $interface->get('Application')->shouldHaveBeenCalled();
        $application->getEventManager()->shouldHaveBeenCalled();
    }

    function it_creates_parameterless_controllers(ContainerInterface $interface)
    {
        $this->__invoke($interface, AnnotatedController::class);
    }

}
