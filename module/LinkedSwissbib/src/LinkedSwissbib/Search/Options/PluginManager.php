<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/24/15
 * Time: 4:08 PM
 */

namespace LinkedSwissbib\Search\Options;

use Zend\ServiceManager\ConfigInterface;


class PluginManager extends \VuFind\ServiceManager\AbstractPluginManager
{    /**
 * Constructor
 *
 * @param ConfigInterface $configuration Configuration settings (optional)
 */
    public function __construct(ConfigInterface $configuration = null)
    {
        // These objects are not meant to be shared -- every time we retrieve one,
        // we are building a brand new object.
        $this->setShareByDefault(false);

        parent::__construct($configuration);
    }


    /**
     * Return the name of the base class or interface that plug-ins must conform
     * to.
     *
     * @return string
     */
    protected function getExpectedInterface()
    {
        return 'VuFind\Search\Base\Options';
    }



}