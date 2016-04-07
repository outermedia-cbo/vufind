<?php

namespace LinkedSwissbib\Autocomplete;

use VuFind\Autocomplete\AutocompleteInterface;

class Elasticsearch implements AutocompleteInterface {

    /**
     * Search object family to use
     *
     * @var string
     */
    protected $searchClassId = 'Elasticsearch';

    /**
     * This method returns an array of strings matching the user's query for
     * display in the autocomplete box.
     *
     * @param string $query The user query
     *
     * @return array        The suggestions for the provided query
     */
    public function getSuggestions($query) {

        if (!is_object($this->searchObject)) {
            throw new \Exception('Please set configuration first.');
        }

        // TODO... see \VuFind\Autocomplete\Solr

        return "blubb";
    }

    /**
     * Set parameters that affect the behavior of the autocomplete handler.
     * These values normally come from the search configuration file.
     *
     * @param string $params Parameters to set
     *
     * @return void
     */
    public function setConfig($params) {

        // TODO... see \VuFind\Autocomplete\Solr
    }
}