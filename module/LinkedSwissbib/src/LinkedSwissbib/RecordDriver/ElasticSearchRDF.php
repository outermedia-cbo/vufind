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

class ElasticSearchRDF extends AbstractBase
{

    const FALLBACK_LANGUAGE = 'en';
    protected $propertiesToNameSpaces = [
        'rdf:type' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        'dc11:format' => 'http://purl.org/dc/elements/1.1/format',
        'dc:hasPart' => 'http://purl.org/dc/terms/hasPart',
        'dc:issued' => 'http://purl.org/dc/terms/issued',
        'dc:language' => 'http://purl.org/dc/terms/language',
        'dc:title' => 'http://purl.org/dc/terms/title',
        'bibo:isbn10' => 'http://purl.org/ontology/bibo/isbn10',
        'bibo:isbn13' => 'http://purl.org/ontology/bibo/isbn13',
        'ns0:contentType' => 'http://rdaregistry.info/Elements/u/contentType',
        'ns0:mediaType' => 'http://rdaregistry.info/Elements/u/mediaType',
        'ns0:noteOnResource' => 'http://rdaregistry.info/Elements/u/noteOnResource',
        'ns0:placeOfPublication' => 'http://rdaregistry.info/Elements/u/placeOfPublication',
        'ns0:publicationStatement' => 'http://rdaregistry.info/Elements/u/publicationStatement',
        'rdfs:isDefinedBy' => 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy'

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
        if (isset($this->fields['_source']['dct:language'])) {
            return is_array($this->fields['_source']['dct:language']) ? array_values($this->fields['_source']['dct:language']) :
                [$this->fields['_source']['dct:language']];
        } else {
            return [];
        }

    }

    /*
    public function getFirstNameContributor()
    {
        return $this->fields['_source']['foaf:firstName'];
    }

    public function getLastNameContributor()
    {
        return $this->fields['_source']['foaf:lastName'];
    }
    */

    public function getName()
    {
        $array = $this->fields['_source']['dct:contributor']['foaf:Person'];

        if (isset($this->fields['_source']['dct:contributor']['foaf:Person']['foaf:firstName']) || isset($this->fields['_source']['dct:contributor']['foaf:Person']['foaf:lastName'])) {
            foreach ($array as $key => $item) {
                if ($key == 'foaf:firstName') {
                    $firstName = $item . " ";
                }
            }
            foreach ($array as $key => $item) {
                if ($key == 'foaf:lastName') {
                    $lastName = $item;
                }
            }
            return $firstName . " " . $lastName;
        } else {
            for ($i = 0; $i <= count($array); $i++) {
                foreach ($array[$i] as $key => $item1) {
                    if ($key == 'foaf:firstName') {
                        $name .= $item1 . " ";
                    }
                }
                foreach ($array[$i] as $key => $item2) {
                    if ($key == 'foaf:lastName') {
                        $name .= $item2 . "; ";
                    }
                }
            }
        }
        $name = rtrim($name, "; ");
        return $name;
    }

    public function getThumbnail()
    {
        $array = $this->fields['_source']['dbp:thumbnail'];
        if (!isset($array)) {
            return "../themes/linkedswissbib/images/personAvatar.png";
        } elseif (!is_array($array)) {
            return $array;
        } else {
            for ($i = 0; $i <= 0; $i++) {
                $thumbnail = $array[$i];
            }
            return $thumbnail;
        }
    }
/*
    public function getFirstName()
    {
        return $this->fields['_source']['foaf:firstName'];
    }

    public function getLastName()
    {
        return $this->fields['_source']['foaf:lastName'];
    }*/

    /* independent from given language */
    public function getAlternativeNames()
    {
        $array = $this->fields['_source']['schema:alternateName'];

        if (!isset($array)) {
            return "No content";
        } elseif (!is_array($array)) {
            return $array;
        } elseif (count($array)===1) {
            $result = reset($array);
            return $result;
        } else {
            foreach ($array as $outerarray => $innerarray) {
                    $result .= $innerarray . ", ";
            }
            $result = rtrim($result, ", ");
            return $result;
        }

    }

    public function getBiography($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'dbp:abstract');
    }

    public function getBirthDate()
    {
        $date = $this->fields['_source']['dbp:birthDate'];
        if(isset($date)) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                $result = date("d.m.Y");
                return $result  . ", ";
            } elseif (preg_match("/^[0-9]{4}$/", $date)) {
                $result = $date;
                return $result  . ", ";
            } else {
                return "No content, ";
            }
        } elseif(!isset($date)) {
            $date = $this->fields['_source']['schema:birthDate'];
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                $result = date("d.m.Y");
                return $result . ", ";
            } elseif (preg_match("/^[0-9]{4}$/", $date)) {
                $result = $date;
                return $result . ", ";
            } else {
                return "No content, ";
            }
        } else {
            return "No content, ";
        }
    }

    public function getBirthPlace($lang = 'de') {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpBirthPlaceAsLiteral');
    }

    public function getDeathDate()
    {
        $date = $this->fields['_source']['dbp:deathDate'];
        if(isset($date)) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                $result = date("d.m.Y");
                return $result . ", ";
            } elseif (preg_match("/^[0-9]{4}$/", $date)) {
                $result = $date;
                return $result . ", ";
            } else {
                return "No content, ";
            }
        } elseif(!isset($date)) {
            $date = $this->fields['_source']['schema:deathDate'];
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                $result = date("d.m.Y");
                return $result . ", ";
            } elseif (preg_match("/^[0-9]{4}$/", $date)) {
                $result = $date;
                return $result . ", ";
            } else {
                return "No content, ";
            }
        } else {
            return "No content, ";
        }
    }

    public function getDeathPlace($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpDeathPlaceAsLiteral');
    }

    public function getGenre($lang = 'de') {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpGenreAsLiteral');
    }

    public function getInfluenced($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpInfluenced');
    }

    public function getInfluencedBy($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpInfluencedBy');
    }

    public function getMovement($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpMovementAsLiteral');
    }

    public function getNameAsLabel()
    {
        $firstName = $this->fields['_source']['foaf:firstName'] . " ";
        $lastName = $this->fields['_source']['foaf:lastName'];
        $name = $this->fields['_source']['foaf:name'];

        if (isset($lastName)) {
            $result = $firstName . $lastName;
            return $result;
        } elseif(isset($name)) {
            $result = $name;
            return $result;
        } else {
            return "No content";
        }
    }

    public function getNationality($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpNationalityAsLiteral');
    }

    public function getNotableWork($lang = 'de'){
        return $result = $this->getValueForProperty($lang, 'dbp:notableWork');
    }

    public function getOccupation($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpOccupationAsLiteral');
    }

    public function getPartner($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpPartnerAsLiteral');
    }

    public function getPseudonym($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'dbp:pseudonym');
    }

    public function getSpouse($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpSpouseAsLiteral');
    }

    /* gets value for language dependent public functions */
    private function getValueForProperty($lang, $property)
    {
        $array = $this->fields['_source'][$property];
        return $this->getValueFromArray($lang, $array);
    }

    /* gets values from array for language dependent public functions */
    private function getValueFromArray($lang, $array)
    {
        if (!isset($array)) {
            return "No content";
        } elseif (!is_array($array)) {
            return $array;
        } elseif (count($array)===1) {
            /* returns first result independent from language if array consists of only on object */
            $result = reset($array);
            return $result;
        } else {
            foreach ($array as $outerarray => $innerarray) {
                if (array_key_exists($lang, $innerarray)) {
                    /* returns value in given language if value exists in given language */
                    $result .= $innerarray[$lang] . ", ";
                } elseif (array_key_exists(FALLBACK_LANGUAGE, $innerarray)) {
                    /* returns value in English if value does not exist in given language */
                    $fallbackResult .= $innerarray[FALLBACK_LANGUAGE] . ", ";
                }
            }
            if (empty($result)) {
                if (!empty($fallbackResult)) {
                    $result = $fallbackResult;
                } else {
                    return "No content";
                }
            }
            $result = rtrim($result, ", ");
            return $result;
        }
    }

public
function getSource()
{
    $array = $this->fields['_source']['owl:sameAs'];
    if (!isset($array)) {
        return "No content";
    } elseif (!is_array($array)) {
        return $this->fields['_source']['owl:sameAs'];
    } else {
        for ($i = 0; $i <= count($array); $i++) {
            $source .= $array[$i] . ", ";
        }
        $source = rtrim($source, ", ");
        return $source;
    }
}

/*    public function getFirstNameResources()
    {
        return $this->fields['_source']['dc:contributor']['foaf:Person']['foaf:firstName'];
    }

    public function getLastNameResources()
    {
        return $this->fields['_source']['dc:contributor']['foaf:Person']['foaf:lastName'];
    }*/

public
function getPublicationStatement()
{
    return $this->fields['_source']['rdau:P60333'];
}

public
function getFormat()
{
    return $this->fields['_source']['dc:format'];
}

public
function getTitle()
{
    return $this->fields['_source']['dct:title'];
}

public
function getStatementOfResponsibility()
{
    return $this->fields['_source']['rdau:P60339'];
}

public
function getWorkTitle()
{
    $array = $this->fields['_source']['dct:title'];
    if (!isset($array)) {
        return "No content";
    } elseif (!is_array($array)) {
        return $this->fields['_source']['dct:title'];
    } else {
        for ($i = 0; $i <= 0; $i++) {
            $workTitle .= $array[$i];
        }
        return $workTitle;
    }
}


// TODO: gibt immer nur ein Element zurück, da return die Funktion sofort beendet
public
function getWorkInstances()
{
    $array = $this->fields['_source']['bf:hasInstance'];
    for ($i = 0; $i <= count($array); $i++) {
        foreach ($array[$i] as $key => $item) {
            $instances[] = $item;
            return $instances[$i];
        }
    }
}

public
function getType()
{
    return $this->fields['_source']['rdf:type']["@id"];
}

public
function getCover()
{
    $about = $this->fields['_source']['rdfs:isDefinedBy']['@id'];
    $id = substr($about, 33, 9);
    $url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
    $url_end = '&size=small';
    $link_cover = $url_start . $id . $url_end;
    return $link_cover;
}

public
function getISBN10()
{
    if (isset($this->fields['_source']['bibo:isbn10'])) {
        $isbn10 = $this->fields['_source']['bibo:isbn10'];
        $url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
        $url_end = '&size=small';
        $link_cover = $url_start . $isbn10 . $url_end;
        return $link_cover;
    } elseif ($this->fields['_source']['rdf:type']['@id'] == "http://purl.org/ontology/bibo/Article") {
        return "../themes/linkedswissbib/images/icon_article.png";
    } else {
        return "../themes/linkedswissbib/images/icon_no_image_available.gif";
    }
}

}