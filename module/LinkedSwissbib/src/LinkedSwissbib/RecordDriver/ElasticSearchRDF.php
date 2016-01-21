<?php
/**
 *
 * @category linked-swissbib
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\RecordDriver;
use VuFind\RecordDriver\AbstractBase;

class ElasticSearchRDF extends AbstractBase {

    protected $propertiesToNameSpaces = [
        'rdf:type'  => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        'dc11:format'   => 'http://purl.org/dc/elements/1.1/format',
        'dc:hasPart'    => 'http://purl.org/dc/terms/hasPart',
        'dc:issued' =>  'http://purl.org/dc/terms/issued',
        'dc:language'   =>  'http://purl.org/dc/terms/language',
        'dc:title'  =>  'http://purl.org/dc/terms/title',
        'bibo:isbn10'   =>  'http://purl.org/ontology/bibo/isbn10',
        'bibo:isbn13'   =>  'http://purl.org/ontology/bibo/isbn13',
        'ns0:contentType'   =>  'http://rdaregistry.info/Elements/u/contentType',
        'ns0:mediaType' =>  'http://rdaregistry.info/Elements/u/mediaType',
        'ns0:noteOnResource'    =>  'http://rdaregistry.info/Elements/u/noteOnResource',
        'ns0:placeOfPublication'    =>  'http://rdaregistry.info/Elements/u/placeOfPublication',
        'ns0:publicationStatement'  =>  'http://rdaregistry.info/Elements/u/publicationStatement',
        'rdfs:isDefinedBy'  =>  'http://www.w3.org/2000/01/rdf-schema#isDefinedBy'

    ];


    /**
     * Get text that can be displayed to represent this record in breadcrumbs.
     *
     * @return string Breadcrumb text to represent this record.
     */
    public function getBreadcrumb()
    {
        throw new \Exception(__FUNCTION__ . ' currently not supported');
    }

    /**
     * Return the unique identifier of this record for retrieving additional
     * information (like tags and user comments) from the external MySQL database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        return $this->fields['_source']['@id'];
    }


    public function getRdf($rdfType = 'turtle')
    {

        $rdfGraph = new \EasyRdf_Graph();
        try {
            $rdfGraph->parse(json_encode($this->fields['_source']), 'jsonld');
            return $rdfGraph->serialise($rdfType);
        } catch (\Exception $ex) {
            return "";
        }

    }

/*
    public function getRdfType()
    {
        if (isset($this->fields['_source']['@type']))
        {
            return is_array($this->fields['_source']['@type']) ? $this->fields['_source']['@type'] :
                [$this->fields['_source']['@type']];
        } else {
            return [];
        }
    }*/

    public function getRdfType()
    {
        $rdfType = $this->fields['_source']['rdf:type']['@id'];
        $rdfType = substr_replace($rdfType, " ", 0, 30);
        return $rdfType;
    }

    public function getLanguage()
    {
        if (isset($this->fields['_source']['dct:language']))
        {
            return is_array($this->fields['_source']['dct:language']) ? array_values($this->fields['_source']['dct:language']) :
                [$this->fields['_source']['dct:language']];
        } else {
            return [];
        }

    }

    public function getFirstNameContributor()
    {
        return $this->fields['_source']['foaf:firstName'];
    }

    public function getLastNameContributor()
    {
        return $this->fields['_source']['foaf:lastName'];
    }

    public function getName()
    {
        $array = $this->fields['_source']['dc:contributor']['foaf:Person'];

        if (isset($this->fields['_source']['dc:contributor']['foaf:Person']['foaf:firstName']) || isset($this->fields['_source']['dc:contributor']['foaf:Person']['foaf:lastName'])) {
            foreach ($array as $key => $item) {
                if ($key == 'foaf:firstName' ) {
                    $firstName = $item . " ";
                }
            }
            foreach ($array as $key => $item) {
                if ($key == 'foaf:lastName' ) {
                    $lastName = $item;
                }
            }
            return $firstName . " " . $lastName;
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item1) {
                    if ($key == 'foaf:firstName' ) {
                        $name .= $item1 . " ";
                    }
                }
                foreach ($array[$i] as $key => $item2) {
                    if ($key == 'foaf:lastName' ) {
                        $name .= $item2 . "; ";
                    }
                }
            }
        }
        $name = rtrim($name, "; ");
        return $name;
    }

    public function getFirstName()
    {
        return $this->fields['_source']['foaf:firstName'];
    }

    public function getLastName()
    {
        return $this->fields['_source']['foaf:lastName'];
    }

    public function getOccupation()
    {
        $array = $this->fields['_source']['dbp:occupation'];
        if (isset($this->fields['_source']['dbp:occupation']['@id'])) {
            foreach ($array as $key => $item) {
                    $occupation = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                        $occupation .= $item . ", ";
                }
            }
        }
        $occupation = rtrim($occupation, ", ");
        return $occupation;
    }

    public function getNationality()
    {
        $array = $this->fields['_source']['dbp:nationality'];
        if (isset($this->fields['_source']['dbp:nationality']['@id'])) {
            foreach ($array as $key => $item) {
                $nationality = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $nationality .= $item . ", ";
                }
            }
        }
        $nationality = rtrim($nationality, ", ");
        return $nationality;
    }

    public function getBirthDate()
    {
        $array = $this->fields['_source']['dbp:birthYear'];
        if (!is_array($array)){
            return $this->fields['_source']['dbp:birthYear'];
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                $birthDate .= $array[$i] . " oder ";
            }
            $birthDate = rtrim($birthDate, " oder ");
            return $birthDate;
        }
    }

    public function getBirthPlace()
    {
        $array = $this->fields['_source']['dbp:birthPlace'];
        if (isset($this->fields['_source']['dbp:birthPlace']['@id'])) {
            foreach ($array as $key => $item) {
                $birthPlace = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $birthPlace .= $item . " oder ";
                }
            }
        }
        $birthPlace = rtrim($birthPlace, " oder ");
        return $birthPlace;
    }

    public function getDeathDate()
    {
        $array = $this->fields['_source']['dbp:deathYear'];
        if (!is_array($array)){
            return $this->fields['_source']['dbp:deathYear'];
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                $deathDate .= $array[$i] . " oder ";
            }
            $deathDate = rtrim($deathDate, " oder ");
            return $deathDate;
        }
    }

    public function getDeathPlace()
    {
        $array = $this->fields['_source']['dbp:deathPlace'];
        if (isset($this->fields['_source']['dbp:deathPlace']['@id'])) {
            foreach ($array as $key => $item) {
                $birthPlace = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $deathPlace .= $item . " oder ";
                }
            }
        }
        $deathPlace = rtrim($deathPlace, " oder ");
        return $deathPlace;
    }

    public function getGenre()
    {
        $array = $this->fields['_source']['dbp:genre'];
        if (isset($this->fields['_source']['dbp:genre']['@id'])) {
            foreach ($array as $key => $item) {
                $genre = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $genre .= $item . ", ";
                }
            }
        }
        $genre = rtrim($genre, ", ");
        return $genre;
    }

    public function getMovement()
    {
        $array = $this->fields['_source']['dbp:movement'];
        if (isset($this->fields['_source']['dbp:movement']['@id'])) {
            foreach ($array as $key => $item) {
                $movement = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $movement .= $item . ", ";
                }
            }
        }
        $movement = rtrim($movement, ", ");
        return $movement;
    }

    public function getInfluenced()
    {
        $array = $this->fields['_source']['dbp:influenced'];
        if (isset($this->fields['_source']['dbp:influenced']['@id'])) {
            foreach ($array as $key => $item) {
                $influenced = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $influenced .= $item . ", ";
                }
            }
        }
        $influenced = rtrim($influenced, ", ");
        return $influenced;
    }

    public function getInfluencedBy()
    {
        $array = $this->fields['_source']['dbp:influencedBy'];
        if (isset($this->fields['_source']['dbp:influencedBy']['@id'])) {
            foreach ($array as $key => $item) {
                $influencedBy = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $influencedBy .= $item . ", ";
                }
            }
        }
        $influencedBy = rtrim($influencedBy, ", ");
        return $influencedBy;
    }

    public function getPartner()
    {
        $array = $this->fields['_source']['dbp:partner'];
        if (isset($this->fields['_source']['dbp:partner']['@id'])) {
            foreach ($array as $key => $item) {
                $partner = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $partner .= $item . ", ";
                }
            }
        }
        $partner = rtrim($partner, ", ");
        return $partner;
    }

    public function getSpouse()
    {
        $array = $this->fields['_source']['dbp:spouse'];
        if (isset($this->fields['_source']['dbp:spouse']['@id'])) {
            foreach ($array as $key => $item) {
                $spouse = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $spouse .= $item . ", ";
                }
            }
        }
        $spouse = rtrim($spouse, ", ");
        return $spouse;
    }

    public function getNotableWork()
    {
        $array = $this->fields['_source']['dbp:notableWork'];
        if (isset($this->fields['_source']['dbp:notableWork']['@id'])) {
            foreach ($array as $key => $item) {
                $notableWork = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $notableWork .= $item . ", ";
                }
            }
        }
        $notableWork = rtrim($notableWork, ", ");
        return $notableWork;
    }

    public function getAlternativeNames()
    {
        $array = $this->fields['_source']['dbp:alternativeNames'];
        if (isset($this->fields['_source']['dbp:alternativeNames']['@id'])) {
            foreach ($array as $key => $item) {
                $alternativeNames = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $alternativeNames .= $item . "<br> ";
                }
            }
        }
        $alternativeNames = rtrim($alternativeNames, "<br> ");
        return $alternativeNames;
    }

    public function getPseudonym()
    {
        $array = $this->fields['_source']['dbp:pseudonym'];
        if (isset($this->fields['_source']['dbp:pseudonym']['@id'])) {
            foreach ($array as $key => $item) {
                $pseudonym = $item;
            }
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                    $pseudonym .= $item . "<br> ";
                }
            }
        }
        $pseudonym = rtrim($pseudonym, "<br> ");
        return $pseudonym;
    }


        /*    public function getFirstNameResources()
            {
                return $this->fields['_source']['dc:contributor']['foaf:Person']['foaf:firstName'];
            }

            public function getLastNameResources()
            {
                return $this->fields['_source']['dc:contributor']['foaf:Person']['foaf:lastName'];
            }*/

    public function getPublicationStatement()
    {
        return $this->fields['_source']['rdau:P60333'];
    }

    public function getFormat()
    {
        return $this->fields['_source']['dc:format'];
    }

    public function getTitle()
    {
        return $this->fields['_source']['dct:title'];
    }

    public function getStatementOfResponsibility    ()
    {
        return $this->fields['_source']['rdau:statementOfResponsibility'];
    }

    public function getWorkTitle()
    {
        $workTitle = $this->fields['_source']['dct:title'][0];
        return $workTitle;
    }

    // TODO: gibt immer nur ein Element zurück, da return die Funktion sofort beendet
    public function getWorkInstances()
    {
        $array = $this->fields['_source']['bf:hasInstance'];

        for ($i = 0; $i <= count($array); $i++) {
            foreach ($array[$i] as $key => $item) {
                $instances[] = $item;
                return $instances[$i];
            }
        }
    }

    /* Ergänzungen laufen noch nicht - Vorlage für die Integration einer zweiten Suchabfrage

    public function getWorkInstances()
        {
            $array = $this->fields['_source']['bf:hasInstance'];

            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item) {
                        $instances[] = $item;
                }
            }
            $blub = [
                'index' => 'testsb',
                'type' => 'bibliographicResource',
                'body' => [
                    'query' => [
                        'filtered' => [
                            'filter' => [
                                'terms' => [
                                    '_id' => '$instances'
                                 ]
                             ]
                        ]
                    ]
                ]
            ];
            public function search($blub){}
         }*/

    public function getType()
    {
        return $this->fields['_source']['rdf:type']["@id"];
    }

    public function getCover()
    {
       $about = $this->fields['_source']['rdfs:isDefinedBy']['@id'];
       $id = substr($about, 33, 9);
       $url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
       $url_end = '&size=small';
       $link_cover = $url_start.$id.$url_end;
       return $link_cover;
    }

    public function getISBN10()
    {
        if (isset($this->fields['_source']['bibo:isbn10']))
        {
            $isbn10 = $this->fields['_source']['bibo:isbn10'];
            $url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
            $url_end = '&size=small';
            $link_cover = $url_start.$isbn10.$url_end;
            return $link_cover;
        } elseif ($this->fields['_source']['rdf:type']['@id'] == "http://purl.org/ontology/bibo/Article") {
            return "../themes/linkedswissbib/images/icon_article.png";
        } else {
            return "../themes/linkedswissbib/images/icon_no_image_available.gif";
        }
    }

}