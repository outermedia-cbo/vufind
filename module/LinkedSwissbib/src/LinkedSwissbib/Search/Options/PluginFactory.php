<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/24/15
 * Time: 2:59 PM
 */

namespace LinkedSwissbib\Search\Options;


use Zend\ServiceManager\ServiceLocatorInterface;


class PluginFactory extends \VuFind\ServiceManager\AbstractPluginFactory
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'LinkedSwissbib\Search';
        $this->classSuffix = '\Options';
    }

    /**
     * Create a service for the specified name.
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     * @param string                  $name           Name of service
     * @param string                  $requestedName  Unfiltered name of service
     *
     * @return object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator,
                                          $name, $requestedName
    ) {
        $class = $this->getClassName($name, $requestedName);
        return new $class(
            $serviceLocator->getServiceLocator()->get('VuFind\Config')
        );
    }



}