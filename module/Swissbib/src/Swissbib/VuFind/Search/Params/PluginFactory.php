<?php
/**
 * PluginFactory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Params
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Search\Params;

use Zend\ServiceManager\ServiceLocatorInterface;

use VuFind\Search\Params\PluginFactory as VuFindParamsPluginFactory;

use Swissbib\VuFind\Search\Helper\ExtendedSolrFactoryHelper;

/**
 * PluginFactory
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Params
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PluginFactory extends VuFindParamsPluginFactory
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
     * @param array                   $extraParams    Extra constructor parameters
     * (to follow the Options object and config loader)
     *
     * @return object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator,
        $name, $requestedName, array $extraParams = []
    ) {
        $options   = $serviceLocator->getServiceLocator()
            ->get('VuFind\SearchOptionsPluginManager')->get($requestedName);

        $extendedTargetHelper = $serviceLocator->getServiceLocator()
            ->get('Swissbib\ExtendedSolrFactoryHelper');

        $this->defaultNamespace = $extendedTargetHelper
            ->getNamespace($name, $requestedName);

        $authManager = $serviceLocator->getServiceLocator()->get(
            'VuFind\AuthManager'
        );
        $labelMappingHelper = $serviceLocator->getServiceLocator()->get(
            'Swissbib\TypeLabelMappingHelper'
        );
        $favoriteInstitutionsManager = $serviceLocator->getServiceLocator()->get(
            'Swissbib\FavoriteInstitutions\Manager'
        );
        $class = $this->getClassName($name, $requestedName);
        $configLoader = $serviceLocator->getServiceLocator()->get('VuFind\Config');
        // Clone the options instance in case caller modifies it:
        return new $class(clone($options), $configLoader, $authManager,
            $labelMappingHelper, $favoriteInstitutionsManager,
            ...$extraParams);

    }
}
