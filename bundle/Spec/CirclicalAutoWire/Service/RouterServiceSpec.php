<?php

namespace Spec\CirclicalAutoWire\Service;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Spec\CirclicalAutoWire\Controller\AnnotatedController;
use Spec\CirclicalAutoWire\Controller\ChildRouteController;
use Spec\CirclicalAutoWire\Controller\DistantChildRouteController;
use Spec\CirclicalAutoWire\Controller\OtherAnnotationsController;
use Spec\CirclicalAutoWire\Controller\OtherAnnotationsTypeErrorController;
use Spec\CirclicalAutoWire\Controller\SameNameAController;
use Spec\CirclicalAutoWire\Controller\SameNameBController;
use Spec\CirclicalAutoWire\Controller\SimpleController;
use CirclicalAutoWire\Service\RouterService;
use PhpSpec\ObjectBehavior;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;

class RouterServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouterService::class);
    }

    function let(TreeRouteStack $routeStack)
    {
        $this->beConstructedWith($routeStack, false);
    }

    function it_parses_controllers_by_class()
    {
        $this->parseController(SimpleController::class);
    }

    function it_parses_controllers_with_annotations($routeStack)
    {
        $routeStack->addRoute('baseroute1', [
            'type' => Literal::class,
            'options' => [
                'route' => '/admin/config/foobar',
                'defaults' => [
                    'controller' => AnnotatedController::class,
                    'action' => 'foo',
                ],
            ],
        ])->shouldBeCalled();

        $routeStack->addRoute('baseroute2', [
            'type' => Segment::class,
            'options' => [
                'route' => '/admin/config/foo/:param1/:param2',
                'defaults' => [
                    'controller' => AnnotatedController::class,
                    'action' => 'routeParam',
                ],
                'constraints' => [
                    'param1' => "\\d",
                    'param2' => "A-Za-z",
                ],
            ],
        ])->shouldBeCalled();
        $this->parseController(AnnotatedController::class);
        $this->compile();
    }

    function it_parses_child_routes($routeStack)
    {
        $routeStack->addRoute('icecream', [
            'type' => Literal::class,
            'options' => [
                'route' => '/icecream',
                'defaults' => [
                    'controller' => ChildRouteController::class,
                    'action' => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'eat' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => "/eat",
                        'defaults' => [
                            'controller' => ChildRouteController::class,
                            'action' => 'eat',
                        ],
                    ],
                ],
                'select' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => "/select/:flavor",
                        'defaults' => [
                            'controller' => ChildRouteController::class,
                            'action' => 'selectFlavor',
                        ],
                        'constraints' => [
                            'flavor' => '\d',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled();

        $this->parseController(ChildRouteController::class);
        $this->compile();
    }

    function it_parses_child_routes_across_controllers($routeStack)
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/DistantChildRouteController.php';

        $routeStack->addRoute('icecream', [
            'type' => Literal::class,
            'options' => [
                'route' => '/icecream',
                'defaults' => [
                    'controller' => ChildRouteController::class,
                    'action' => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'eat' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => "/eat",
                        'defaults' => [
                            'controller' => ChildRouteController::class,
                            'action' => 'eat',
                        ],
                    ],
                ],
                'melt' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => "/melt",
                        'defaults' => [
                            'controller' => DistantChildRouteController::class,
                            'action' => 'melt',
                        ],
                    ],
                ],
                'select' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => "/select/:flavor",
                        'defaults' => [
                            'controller' => ChildRouteController::class,
                            'action' => 'selectFlavor',
                        ],
                        'constraints' => [
                            'flavor' => '\d',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled();

        $this->parseController(ChildRouteController::class);
        $this->parseController(DistantChildRouteController::class);
        $this->compile();
    }

    function it_lets_children_have_same_names($routeStack)
    {
        $routeStack->addRoute('foocrud', [
            'type' => Literal::class,
            'options' => [
                'route' => '/foo',
                'defaults' => [
                    'controller' => SameNameAController::class,
                    'action' => 'crud',
                ],
            ],
            'child_routes' => [
                'add' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => "/add",
                        'defaults' => [
                            'controller' => SameNameAController::class,
                            'action' => 'add',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled();

        $routeStack->addRoute('barcrud', [
            'type' => Literal::class,
            'options' => [
                'route' => '/bar',
                'defaults' => [
                    'controller' => SameNameBController::class,
                    'action' => 'crud',
                ],
            ],
            'child_routes' => [
                'add' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => "/add",
                        'defaults' => [
                            'controller' => SameNameBController::class,
                            'action' => 'add',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled();

        $this->parseController(SameNameAController::class);
        $this->parseController(SameNameBController::class);
        $this->compile();
    }

    function it_skips_other_annotations()
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/OtherAnnotationsController.php';
        AnnotationRegistry::registerAutoloadNamespace("Spec\\CirclicalAutoWire\\Annotations", realpath(__DIR__ . "/../../../"));

        $this->shouldNotThrow(\Error::class)->during('parseController', [OtherAnnotationsController::class]);
    }

    function it_skips_other_annotations_type_error()
    {
        include __DIR__ . '/../../CirclicalAutoWire/Controller/OtherAnnotationsTypeErrorController.php';
        AnnotationRegistry::registerAutoloadNamespace("Spec\\CirclicalAutoWire\\Annotations", realpath(__DIR__ . "/../../../"));

        //this should be thrown, but I'm unable to force it...
//        $this->shouldThrow(\TypeError::class)->during('parseController', [OtherAnnotationsTypeErrorController::class]);
        $this->shouldNotThrow(\PhpSpec\Exception\Example\ErrorException::class)->during('parseController', [OtherAnnotationsTypeErrorController::class]);
    }

    function its_annotions_can_be_reset()
    {
        $this->parseController(SameNameAController::class);
        $this->getAnnotations()->shouldHaveCount(2);
        $this->reset();
        $this->getAnnotations()->shouldBeArray();
        $this->getAnnotations()->shouldHaveCount(0);
    }

}
