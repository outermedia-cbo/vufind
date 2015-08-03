<?php

namespace LinkedSwissbib\Controller;
use VuFind\Controller\AbstractSearch;

class ExploreAuthorController extends AbstractSearch {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchClassId = 'Elasticsearch';
        parent::__construct();
    }


    public function showAction ()
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