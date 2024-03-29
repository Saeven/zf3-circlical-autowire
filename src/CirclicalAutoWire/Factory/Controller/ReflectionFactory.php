<?php

declare(strict_types=1);

namespace CirclicalAutoWire\Factory\Controller;

use Exception;
use Laminas\EventManager\EventManager;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Validator\ValidatorPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;

use function array_key_exists;
use function preg_match;
use function substr;

final class ReflectionFactory implements AbstractFactoryInterface
{
    /**
     * These aliases work to substitute class names with SM types that are buried in ZF.
     *
     * @var array
     */
    private static array $aliases = [
        ValidatorPluginManager::class => 'ValidatorManager',
        FormElementManager::class => 'FormElementManager',
        /* using strings since they're not required by package composer */
        'ZfcTwig\View\TwigRenderer' => 'TemplateRenderer',
        'Laminas\Mvc\I18n\Translator' => 'MvcTranslator',
    ];

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return substr($requestedName, -10) === 'Controller';
    }

    /**
     * @return object
     * @inheritDoc
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $class = new ReflectionClass($requestedName);
        $parentLocator = $container;

        if ($constructor = $class->getConstructor()) {
            if ($parameters = $constructor->getParameters()) {
                $parameterInstances = [];

                foreach ($parameters as $parameter) {
                    if ($parameter->getType()) {
                        if ($parameter->getName() === 'config' && $parameter->getType()->getName() === 'array') {
                            $parameterInstances[] = $parentLocator->get('config');
                            continue;
                        }

                        $className = $parameter->getType()->getName();
                        if (array_key_exists($className, static::$aliases)) {
                            $className = static::$aliases[$className];
                        }

                        try {
                            if (preg_match("/([[:alpha:]]+)\\\\Form\\\\/u", $className)) {
                                $parameterInstances[] = $parentLocator->get('FormElementManager')->get($className);
                            } elseif ($className === EventManager::class) {
                                $parameterInstances[] = $parentLocator->get('Application')->getEventManager();
                            } else {
                                $parameterInstances[] = $parentLocator->get($className);
                            }
                        } catch (Exception $exception) {
                            echo $exception->getMessage();
                            die(self::class . " couldn't create an instance of <b>$className</b> to satisfy the constructor for <b>$requestedName</b> at param $parameter.");
                        }
                    }
                }

                return $class->newInstanceArgs($parameterInstances);
            }
        }

        return new $requestedName();
    }
}
