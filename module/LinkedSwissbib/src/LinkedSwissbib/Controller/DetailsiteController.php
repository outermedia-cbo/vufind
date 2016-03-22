<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 23.02.16
 * Time: 14:07
 */

namespace LinkedSwissbib\Controller;

use VuFind\Controller\AbstractSearch;

class DetailsiteController extends AbstractSearch
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchClassId = 'detailsite';
        parent::__construct();
    }


    public function searchAction()
    {
        return parent::resultsAction();

    }

    public function authorAction()
    {
        return parent::resultsAction();

    }

    /**
     * Convenience method for accessing results
     *
     * @return \LinkedSwissbib\Search\Results\PluginManager
     */
    protected function getResultsManager()
    {
        return $this->getServiceLocator()->get('LinkedSwissbib\SearchResultsPluginManager');
    }

}