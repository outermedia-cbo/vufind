<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 01.04.16
 * Time: 14:42
 */
namespace LinkedSwissbib\VuFind\Autocomplete;
use Zend\ServiceManager\ServiceManager;

class Factory {

    public static function getElasticSearch(ServiceManager $sm)
    {
        return new ElasticSearch (
            $sm->getServiceLocator()->get('VuFind\SearchResultsPluginManager')
        );
    }
}