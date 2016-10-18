# zf3-circlical-autowire
[![Build Status](https://travis-ci.org/Saeven/zf3-circlical-autowire.svg?branch=master)](https://travis-ci.org/Saeven/zf3-circlical-autowire)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/488fcf3040df4fa4b3ab4b2c15ad5752)](https://www.codacy.com/app/alemaire/zf3-circlical-autowire?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-circlical-autowire&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/488fcf3040df4fa4b3ab4b2c15ad5752)](https://www.codacy.com/app/alemaire/zf3-circlical-autowire?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-circlical-autowire&amp;utm_campaign=Badge_Coverage)
[![Latest Stable Version](https://poser.pugx.org/saeven/zf3-circlical-autowire/v/stable)](https://packagist.org/packages/saeven/zf3-circlical-autowire)
[![Total Downloads](https://poser.pugx.org/saeven/zf3-circlical-autowire/downloads)](https://packagist.org/packages/saeven/zf3-circlical-autowire)


Live the dream, your controller authoring workflow doesn't have to be so painful!
  
##Route using annotations!

You can use annotations right above your Controller actions to automatically plug routes into the ZF3 Router.  No more gear-switching 
to route files, or digging through route config arrays when you are refactoring.

Freeedom!

##Automatic controller factory!

**PLUS**, stop creating a proliferation of controller factories that are all the same!
This module provides a very simple reflection factory for controllers.  Just define your constructors (as you should)
and let the lazy factory do the rest!


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
     * @Route("/freedom/:param", name="/easy-as-pie" constraints={"param":"[a-zA-Z]"}, defaults={"param":"index"})
     */
     public function anyOldNameAction(){
        // ...
     }
     
     
### Controller Annotations

Provided as a convenience, these help you reach a higher level of lazy.  If you know all your Controller routes will start 
with `/index/system`, simply annotate your controller as such:

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

### Development

In this mode, annotations are scanned and read.  There's an incredibly small overhead to scanning Controller code.  Each time you refresh,
you will see the compiled routes file recreated in the location you have specified (see this Module's config file).

### Production

In this mode, annotations are not scanned.  Instead, the config file you created in dev mode is automatically merged with your Zend Framework's
 routes/route config to create a 'traditional' scenario where routes are hardcoded.  Zero module overhead.
 

> I hope you like this module, you can reach out on freenode's #zftalk or @Saeven on Twitter!  All PRs considered!