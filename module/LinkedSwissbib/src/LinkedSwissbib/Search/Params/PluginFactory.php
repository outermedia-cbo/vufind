<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/24/15
 * Time: 2:59 PM
 */

namespace LinkedSwissbib\Search\Params;

use Zend\ServiceManager\ServiceLocatorInterface;


class PluginFactory extends \VuFind\ServiceManager\AbstractPluginFactory
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'LinkedSwissbib\Search';
        $this->classSuffix = '\Params';
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
        $options = $serviceLocator->getServiceLocator()
            ->get('LinkedSwissbib\SearchOptionsPluginManager')->get($requestedName);
        $class = $this->getClassName($name, $requestedName);
        // Clone the options instance in case caller modifies it:
        return new $class(
            clone($options),
            $serviceLocator->getServiceLocator()->get('VuFind\Config')
        );
    }



}