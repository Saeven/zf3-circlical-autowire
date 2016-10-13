<?php

namespace CirclicalAutoWire\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ReflectionFactory implements AbstractFactoryInterface
{

    /**
     * These aliases work to substitute class names with SM types that are buried in ZF
     *
     * @var array
     */
    protected $aliases = [
        'Zend\Validator\ValidatorPluginManager' => 'ValidatorManager',
        'Zend\Mvc\I18n\Translator' => 'MvcTranslator',
        'ZfcTwig\View\TwigRenderer' => 'TemplateRenderer',
        'Zend\Form\FormElementManager\FormElementManagerV3Polyfill' => 'FormElementManager',
    ];

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return substr($requestedName, -10) == 'Controller';
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $class = new \ReflectionClass($requestedName);
        $parentLocator = $container;

        if ($constructor = $class->getConstructor()) {
            if ($parameters = $constructor->getParameters()) {
                $parameterInstances = [];

                foreach ($parameters as $parameter) {

                    if ($parameter->getClass()) {
                        $className = $parameter->getClass()->getName();
                        if (array_key_exists($className, $this->aliases)) {
                            $className = $this->aliases[$className];
                        }

                        try {
                            if (preg_match("/^([[:alpha:]]+)\\\\Form\\\\/us", $className)) {
                                $parameterInstances[] = $parentLocator->get('FormElementManager')->get($className);
                            } else {
                                $parameterInstances[] = $parentLocator->get($className);
                            }
                        } catch (\Exception $exception) {
                            echo $exception->getMessage();
                            die(__CLASS__ . " couldn't create an instance of <b>$className</b> to satisfy the constructor for <b>$requestedName</b> at param $parameter.");
                        }
                    } else {
                        if ($parameter->isArray() && $parameter->getName() == 'config') {
                            $parameterInstances[] = $parentLocator->get('config');
                        } elseif ($parameter->getName() == 'formElementManager') {
                            $parameterInstances[] = $parentLocator->get('FormElementManager');
                        } elseif ($parameter->getName() == 'serviceLocator') {
                            $parameterInstances[] = $container;
                        }
                    }

                }

                return $class->newInstanceArgs($parameterInstances);
            }
        }

        return new $requestedName;
    }
}
