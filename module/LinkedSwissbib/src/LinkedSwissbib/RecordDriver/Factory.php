<?php
/**
 *
 * @category linked-swissbib
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\RecordDriver;
use Zend\ServiceManager\ServiceManager;

class Factory {

    /**
     * @param ServiceManager $sm
     * @return ElasticSearchRDF
     */
    public static function getElasticSearchRdfRecordDriver(ServiceManager $sm)
    {
        $serviceLocator = $sm->getServiceLocator();
        $driver = new ElasticSearchRDF($serviceLocator->get('VuFind\Config')->get('config'),null);
        return $driver;

    }



}