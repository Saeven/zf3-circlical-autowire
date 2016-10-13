<?php

namespace CirclicalAutoWire;

use CirclicalUser\Entity\UserAuthenticationLog;
use CirclicalUser\Listener\AccessListener;
use Zend\Console\Console;
use Zend\Mvc\MvcEvent;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $mvcEvent)
    {

    }

}