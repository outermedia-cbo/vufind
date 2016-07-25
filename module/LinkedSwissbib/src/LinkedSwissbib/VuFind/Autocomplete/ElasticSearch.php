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

        return array_unique($results, SORT_REGULAR);
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

    private function parseDate($date){
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return date("Y");
        } elseif (preg_match("/^[0-9]{4}$/", $date)) {
            return $date;
        }
    }

    private function refine($searchResults){
        $results = array();
        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            $type = $current['_type'];
            if($type != 'person' && $type != 'DEFAULT')
                continue; //TODO: we ignore everything except person and DEFAULT (i.e. subjects) for now
            $id = $current['_source']['@id'];

            $displayname = "";

            if ($type == 'person') {
                if (!isset($current['_source']['dbp:birthDate']) && !isset($current['_source']['schema:birthDate']) && !isset($current['_source']['dbp:birthYear'])) {
                        $birthDate = "?";
                    } elseif (isset($current['_source']['schema:birthDate'])) {
                        $birthDate = $this->parseDate($current['_source']['schema:birthDate']);
                    } elseif (isset($current['_source']['dbp:birthDate'])) {
                        $birthDate = $this->parseDate($current['_source']['dbp:birthDate']);
                    } elseif (isset($current['_source']['dbp:birthYear'])) {
                        $birthDate = $current['_source']['dbp:birthYear'];
                }

                if (!isset($current['_source']['dbp:deathDate']) && !isset($current['_source']['schema:deathDate']) && !isset($current['_source']['dbp:deathYear'])) {
                    $deathDate = "?";
                } elseif (isset($current['_source']['schema:deathDate'])) {
                    $deathDate = $this->parseDate($current['_source']['schema:deathDate']);
                } elseif (isset($current['_source']['dbp:deathDate'])) {
                    $deathDate = $this->parseDate($current['_source']['dbp:deathDate']);
                } elseif (isset($current['_source']['dbp:deathYear'])) {
                    $deathDate = $current['_source']['dbp:deathYear'];
                }

                if (!empty($birthDate) && !empty($deathDate)) {
                    $birthAndDeathDates = ' (' . $birthDate . ' - ' . $deathDate. ')';
                } elseif (!empty($birthDate) && empty($deathDate)) {
                    $birthAndDeathDates = ' (' . $birthDate . ' - ?)';
                } elseif (!empty($deathDate) && empty($birthDate)) {
                    $birthAndDeathDates = ' (? - ' . $deathDate . ')';
                } else {
                    $birthAndDeathDates = "?";
                }

                if(isset($current['_source']['foaf:firstName']) && isset($current['_source']['foaf:lastName']))
                    $displayname = $current['_source']['foaf:firstName'] . ' ' . $current['_source']['foaf:lastName']. $birthAndDeathDates;
                elseif(isset($current['_source']['foaf:lastName']))
                    $displayname =  $current['_source']['foaf:lastName'] . $birthAndDeathDates;
                elseif(isset($current['_source']['foaf:name']))
                    $displayname = $current['_source']['foaf:name'] . $birthAndDeathDates;
                else
                    $displayname = "?";
            }

            if ($type == 'DEFAULT') { // subject
               $displayname = $current['_source']['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'][0]['@value'];
            }

            $results[] = array($id, $type, $displayname);
        }
        return $results;
    }
}