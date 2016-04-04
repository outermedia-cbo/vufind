<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 31.03.16
 * Time: 17:37
 */
namespace LinkedSwissbib\VuFind\Autocomplete;
use  VuFind\Autocomplete\AutocompleteInterface;

class ElasticSearch implements AutocompleteInterface{

    protected $searchClassId = 'Elasticsearch';
    protected $searchObject;


    public function __construct(\VuFind\Search\Results\PluginManager $results)
    {
        $this->resultsManager = $results;
        $this->searchObject = $this->resultsManager->get($this->searchClassId);
    }


    /**
     * This method returns an array of strings matching the user's query for
     * display in the autocomplete box.
     *
     * @param string $query The user query
     *
     * @return array        The suggestions for the provided query
     */
    public function getSuggestions($query){
        // Perform the search:
        $this->searchObject->getParams()->getQuery()->setHandler("AutocompleteAuthor");
        $this->searchObject->getParams()->getQuery()->setString($query);
        $searchResults = $this->searchObject->getResults();

        $results = $this->refine($searchResults);

        return $results;
    }

    /**
     * Set parameters that affect the behavior of the autocomplete handler.
     * These values normally come from the search configuration file.
     *
     * @param string $params Parameters to set
     *
     * @return void
     */
    public function setConfig($params){}

    private function refine($searchResults){
        $results = array();
        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            $type = $current['_type'];
            $id = $current['_source']['@id'];

            if(isset($current['_source']['foaf:firstName']) && isset($current['_source']['foaf:lastName']))
                $displayname = $current['_source']['foaf:firstName'] . ' ' . $current['_source']['foaf:lastName'];
            else
                $displayname = $current['_source']['foaf:name'];

            $results[] = array($id, $type, $displayname);
        }
        return $results;
    }
}