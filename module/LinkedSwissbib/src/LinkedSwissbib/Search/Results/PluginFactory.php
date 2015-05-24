<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Search\Results;

use Zend\ServiceManager\ServiceLocatorInterface;


class PluginFactory extends \VuFind\ServiceManager\AbstractPluginFactory
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'LinkedSwissbib\Search';
        $this->classSuffix = '\Results';
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
        $params = $serviceLocator->getServiceLocator()
            ->get('LinkedSwissbib\SearchParamsPluginManager')->get($requestedName);
        $class = $this->getClassName($name, $requestedName);
        return new $class($params);
    }


}