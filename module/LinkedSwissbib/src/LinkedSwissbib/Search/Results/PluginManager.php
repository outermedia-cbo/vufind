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

use Zend\ServiceManager\ConfigInterface;

class PluginManager extends \VuFind\Search\Results\PluginManager
{
    /**
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
        return 'VuFind\Search\Base\Results';
    }

}