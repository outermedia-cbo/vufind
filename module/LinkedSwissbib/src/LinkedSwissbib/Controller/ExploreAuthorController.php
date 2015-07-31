<?php

namespace LinkedSwissbib\Controller;
use VuFind\Controller\AbstractSearch;

class ExploreAuthorController extends AbstractSearch {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchClassId = 'ExploreAuthor';
        parent::__construct();
    }


    public function showAction ()
    {
        return parent::resultsAction();

    }
}