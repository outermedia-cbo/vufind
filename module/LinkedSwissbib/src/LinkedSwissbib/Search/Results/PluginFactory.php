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
use Swissbib\VuFind\Search\Results\PluginFactory as SwissbibPluginFactory;

use Swissbib\VuFind\Search\Helper\ExtendedSolrFactoryHelper;


class PluginFactory extends SwissbibPluginFactory
{

    /**
     * CanCreateServiceWithName
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocatorInterface
     * @param String                  $name           Name
     * @param String                  $requestedName  RequestedName
     *
     * @return mixed
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator,
                                             $name, $requestedName
    ) {
        /**
         * ExtendedSolrFactoryHelper
         *
         * @var ExtendedSolrFactoryHelper $extendedTargetHelper
         */
        $extendedTargetHelper = $serviceLocator->getServiceLocator()
            ->get('Swissbib\ExtendedSolrFactoryHelper');
        $this->defaultNamespace = $extendedTargetHelper
            ->getNamespace($name, $requestedName);

        return parent::canCreateServiceWithName(
            $serviceLocator, $name, $requestedName
        );
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
        /**
         * ExtendedSolrFactoryHelper
         *
         * @var ExtendedSolrFactoryHelper $extendedTargetHelper
         */
        $extendedTargetHelper = $serviceLocator->getServiceLocator()
            ->get('Swissbib\ExtendedSolrFactoryHelper');
        $this->defaultNamespace = $extendedTargetHelper
            ->getNamespace($name, $requestedName);

        return parent::createServiceWithName($serviceLocator, $name, $requestedName);
    }


}