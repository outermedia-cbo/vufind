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

use Swissbib\VuFind\Search\Results\PluginManager as SwissbibResultsPluginManager;
use Zend\ServiceManager\ConfigInterface;



class PluginManager extends SwissbibResultsPluginManager
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


}