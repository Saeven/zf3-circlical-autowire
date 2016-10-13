# zf3-circlical-autowire
[![Build Status](https://travis-ci.org/Saeven/zf3-circlical-autowire.svg?branch=master)](https://travis-ci.org/Saeven/zf3-circlical-autowire)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/488fcf3040df4fa4b3ab4b2c15ad5752)](https://www.codacy.com/app/alemaire/zf3-circlical-autowire?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-circlical-autowire&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/saeven/zf3-circlical-autowire/v/stable)](https://packagist.org/packages/saeven/zf3-circlical-autowire)
[![Total Downloads](https://poser.pugx.org/saeven/zf3-circlical-autowire/downloads)](https://packagist.org/packages/saeven/zf3-circlical-autowire)


This module is the dream.  You can use annotations right above your Controller actions to 
automatically plug routes into the ZF3 Router.  No more gear-switching to route files, or digging
through route config arrays when you are refactoring.

Freeedom!


##### This module is at a very nascent stage, it was done to test the case, but I'm lazy, and will pursue this

##Installation

Install with:

    composer require zf3-circlical-autowire
    
Then, add it near the top of your application.config.php

    'CirclicalAutoWire',
    
    
## Usage

In any controller, simply add this use statement:

    use CirclicalAutoWire\Annotations\Route;
    
Then, on any action in that controller, use this annotation:

    /**
     * Your usual stuff here
     * @returns bool
     * @Route("/freedom")
     */
     public function anyOldNameAction(){
        // this beats editing a route file each time!
     }
     
##TODO

* Wire up main route types
* Create a "Production" mode that won't scan controllers on load
* Create a CLI command to compile down your annotations into bona-fide routes for Production