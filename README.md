# Route Auto-Wiring for Zend-MVC
[![Build Status](https://github.com/saeven/zf3-circlical-autowire/actions/workflows/phpspec-task.yml/badge.svg)](https://travis-ci.org/Saeven/zf3-circlical-user)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/74a8233ff1464fada1a333104770705f)](https://www.codacy.com/gh/Saeven/zf3-circlical-autowire/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-circlical-autowire&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/488fcf3040df4fa4b3ab4b2c15ad5752)](https://www.codacy.com/app/alemaire/zf3-circlical-autowire?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-circlical-autowire&amp;utm_campaign=Badge_Coverage)
[![Latest Stable Version](https://poser.pugx.org/saeven/zf3-circlical-autowire/v/stable)](https://packagist.org/packages/saeven/zf3-circlical-autowire)
[![Total Downloads](https://poser.pugx.org/saeven/zf3-circlical-autowire/downloads)](https://packagist.org/packages/saeven/zf3-circlical-autowire)


A zend-mvc module that favors rapid development, that compiles routes to standard PHP arrays for production (and merges them automatically for you too).  Does not compete with standard route declarations (both can be used in tandem).
  
Use annotations right above your actions to automatically plug routes into the ZF3 Router.  No more gear-switching 
to route files, or digging through route config arrays when you are refactoring.

This module also provides a reflection-based abstract factory to automatically wire your controllers using their constructors.
Just define your constructors (as you should) and let the lazy factory do the rest!



##Installation

Install with:

    composer require zf3-circlical-autowire
    
Then, add it near the top of your application.config.php

    'CirclicalAutoWire',
    
    
## Prepping Annotations

In any controller that should use this module, simply add this **use statement**:

    use CirclicalAutoWire\Annotations\Route;
    
### Method Annotations
    
On any action in a controller with the use statement, use these types of annotations:

    <?php
    /**
     * Your usual stuff here
     * @returns bool
     * @Route("/freedom")
     */
     public function anyOldNameAction(){
        // this beats editing a route file each time!
     }
     
    /**
     * This route has a parameter
     * @Route("/freedom/:param")
     */
     public function anyOldNameAction(){
        $this->params()->fromRoute('param');
     }
     
    /**
     * This route has a parameter and a constraint
     * @Route("/freedom/:param", constraints={"param":"[a-zA-Z]"})
     */
     public function anyOldNameAction(){
        // ...
     }
     
    /**
     * Route with parameter, constraint, and defaults
     * @Route("/freedom/:param", constraints={"param":"[a-zA-Z]"}, defaults={"param":"index"})
     */
     public function anyOldNameAction(){
        // ...
     }
     
    /**
     * Route with parameter, name, constraint, and defaults
     * @Route("/freedom/:param", name="easy-as-pie", constraints={"param":"[a-zA-Z]"}, defaults={"param":"index"})
     */
     public function anyOldNameAction(){
        // ...
     }
     
     
### Child Routes
     
Child routes are simple to define.  Define the parent by giving it a name, and whether or not it may terminate.  Separately, tell the child routes that their `parent` is that first route (by way of name).  Here's a complete example:

    <?php
    /**
     * Class ChildRouteController
     * @package Spec\CirclicalAutoWire\Controller
     */
    class ChildRouteController extends AbstractActionController
    {
    
        /**
         * @Route("/icecream", name="icecream", terminate=true)
         */
        public function indexAction(){}
    
        /**
         * This is a sample docblock
         *
         * @Route("/eat", parent="icecream", name="eat")
         */
        public function eatAction(){}
    
    
        /**
         * @Route("/select/:flavor", constraints={"flavor":"\d"}, name="select", parent="icecream")
         */
        public function selectFlavorAction(){}
    
    }

This will produce:

    <?php
    'router' => [
        'routes' => [
            'icecream' => [
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
            ],
        ],
    ],

      
      
     
     
### Controller Annotations

Provided as a convenience, these help you reach a higher level of lazy.  If you know all your Controller routes will start 
with `/index/system`, simply annotate your controller as such:

    <?php
    /**
     * Controller Index
     * @Route("/index/system")
     */
     class IndexController extends AbstractActionController
     {
     
         /**
          * @Route("/update")
          */
         public function updateAction(){
         
         }
         
         /**
          * @Route("/get/:id")
          */
         public function getAction(){
         
         }
     }

In the example above, the following routes will be compiled:

* /index/system/update
* /index/system/get/:id

## Two Modes

Mode is set, via config.

### Development

In this mode, annotations are scanned and read.  There's an incredibly small overhead to scanning Controller code.  Each time you refresh,
you will see the compiled routes file recreated in the location you have specified (see this Module's config file).

### Production

In this mode, annotations are not scanned.  Instead, the config file you created in dev mode is automatically merged with your Zend Framework's
 routes/route config to create a 'traditional' scenario where routes are hardcoded.  Zero module overhead.
 
 **Important Note: When actuated via the console, it will behave as though it were in production mode**
 

> I hope you like this module, you can reach out on freenode's #zftalk or @Saeven on Twitter!  All PRs considered!
