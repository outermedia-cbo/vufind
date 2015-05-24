<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/24/15
 * Time: 1:24 PM
 */

namespace LinkedSwissbib\Service;

use Zend\ServiceManager\ServiceManager;


class Factory
{

    /**
     * Generic plugin manager factory (support method).
     *
     * @param ServiceManager $sm Service manager.
     * @param string         $ns LinkedSwissbib namespace containing plugin manager
     *
     * @return object
     */
    public static function getGenericPluginManager(ServiceManager $sm, $ns)
    {
        $className = 'LinkedSwissbib\\' . $ns . '\PluginManager';
        $configKey = strtolower(str_replace('\\', '_', $ns));
        $config = $sm->get('Config');
        return new $className(
            new \Zend\ServiceManager\Config(
                $config['vufind']['plugin_managers'][$configKey]
            )
        );
    }

    /**
     * Construct the Search\Options Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Options\PluginManager
     */
    public static function getSearchOptionsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Options');
    }

    /**
     * Construct the Search\Params Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Params\PluginManager
     */
    public static function getSearchParamsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Params');
    }


    /**
     * Construct the Search\Results Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    public static function getSearchResultsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'Search\Results');
    }


}