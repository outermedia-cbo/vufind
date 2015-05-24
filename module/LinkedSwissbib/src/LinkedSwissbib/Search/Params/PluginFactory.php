<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Params
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
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