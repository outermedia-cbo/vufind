<?php
/**
 *
 * @category linked-swissbib
 * @package  RecordDriver
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\RecordDriver;

use VuFind\RecordDriver\AbstractBase;

class ElasticSearchRDF extends AbstractBase
{

    const FALLBACK_LANGUAGE = 'en';
    const ARRAY_SEPARATOR = ", ";
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

    private function getValueIfAvailable($lookup, $fallback = "no content provided")
    {
        if (isset($this->fields['_source'][$lookup]))
            $result = $this->fields['_source'][$lookup];
        else
            $result = $fallback;

        if(is_array($result))
            $result = implode(self::ARRAY_SEPARATOR,$result);

        return preg_replace(['/</','/>/'], ['&lt;','&gt;'],$result);
    }

    private function getValueForProperty($lang, $lookup, $fallback = "no content provided")
    {
        if(!isset($this->fields['_source'][$lookup]))
            return $fallback;
        $array = $this->fields['_source'][$lookup];
        return $this->getValueFromArray($lang, $array);
    }

/*    private function getNestedValueForProperty($lang, $lookup, $fallback = "no content provided")
      {
        $result = $this->getValueForProperty($lang, $lookup, $fallback);

        if(is_array($result)) {
            return $result['@value'];
        }
        return $result;
    }*/

    private function getNestedValueForProperty($lang, $lookup, $innerlookup = '@value', $fallback = "no content provided"){
        $result = $this->getValueForProperty($lang, $lookup, $fallback);

        if(is_array($result)) {
            return $result[$innerlookup];
        }
        return $result;
    }

    private function getValueFromArray($lang, $array)
    {
        $result = ""; $fallbackResult = "";
        if (!is_array($array)) {
            return $array;
        } elseif (count($array) === 1) {
            /* returns first result independent from language if array consists of only one object */
            return reset($array);
        } else {
            foreach ($array as $outerarray => $innerarray) {
                if (array_key_exists($lang, $innerarray)) {
                    /* returns value in given language if value exists in given language */
                    $result[] = $innerarray[$lang];
                } elseif (array_key_exists(self::FALLBACK_LANGUAGE, $innerarray)) {
                    /* returns value in English if value does not exist in given language */
                    $fallbackResult[] = $innerarray[self::FALLBACK_LANGUAGE];
                }
            }
            if (empty($result)) {
                if (!empty($fallbackResult)) {
                    $result = $fallbackResult;
                } else {
                    return "no content provided";
                }
            }
            return implode(self::ARRAY_SEPARATOR, $result);
        }
    }

    private function parseDate($date){
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return date("d.m.Y", strtotime($date));
        } elseif (preg_match("/^[0-9]{4}$/", $date)) {
            return $date;
        }
        return "could not parse date!";
    }

    public function getUniqueID()
    {
        return $this->getValueIfAvailable('@id');
    }

    public function getEdition()
    {
        return $this->getValueIfAvailable('bibo:edition');
    }

    //TODO: lang?
    public function getStatementOfResponsibility()
    {
        return $this->getValueIfAvailable('rdau:P60339');
    }

    //TODO: lang?
    public function getTitle()
    {
        return $this->getValueIfAvailable('dct:title');
    }

    public function getYear()
    {
        return $this->getValueIfAvailable('dct:issued');
    }

    //TODO: lang?
    public function getNameWork()
    {
        return $this->getValueIfAvailable('dct:contributor')[0];
    }

    /* place, publisher, year */
    //TODO: lang?
    public function getPublicationStatement()
    {
        return $this->getValueIfAvailable('rdau:P60333');
    }

    /* physical description*/
    public function getFormat()
    {
        return $this->getValueIfAvailable('dc:format');
    }

    public function getFormats() {
        return array('myformat (TODO: in ElasticSearchRDF.php)'); // TODO
    }

    public function displayHoldings() {
        return null; // TODO
    }

    public function displayLinks() {
        return null; // TODO
    }

    public function getInstitutions() {
        return array('dummy'); // TODO
    }

    public function getOpenURL() {
        return null; // TODO
    }

    public function getWorkTitle()
    {
        return $this->getValueIfAvailable('dct:title');
    }

    public function getNationality($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpNationalityAsLiteral');
    }

    public function getNotableWork($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpNotableWorkAsLiteral');
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

    public function getDeathPlace($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpDeathPlaceAsLiteral');
    }

    public function getGenre($lang = 'de')
    {
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

    public function getBiography($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'dbp:abstract');
    }

    public function getBirthPlace($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpBirthPlaceAsLiteral');
    }

    public function getNameAsLabel()
    {
        if(isset($this->fields['_source']['foaf:firstName']) && isset($this->fields['_source']['foaf:lastName']))
            return $this->fields['_source']['foaf:firstName'] . " " . $this->fields['_source']['foaf:lastName'];
        elseif(isset($this->fields['_source']['foaf:lastName']))
            return $this->fields['_source']['foaf:lastName'];
        return $this->getValueIfAvailable('foaf:name');
    }

    public function getBirthDate()
    {
        if (isset($this->fields['_source']['dbp:birthDate'])) {
            return $this->parseDate($this->fields['_source']['dbp:birthDate']);
        } elseif (isset($this->fields['_source']['schema:birthDate'])) {
            return $this->parseDate($this->fields['_source']['schema:birthDate']);
        } elseif (isset($this->fields['_source']['dbp:birthYear'])) {
            return $this->parseDate($this->fields['_source']['dbp:birthYear']);
        } else {
            return "no content provided";
        }
    }

    public function getDeathDate()
    {
        if (isset($this->fields['_source']['dbp:deathDate'])) {
            return $this->parseDate($this->fields['_source']['dbp:deathDate']);
        } elseif (isset($this->fields['_source']['schema:deathDate'])) {
            return $this->parseDate($this->fields['_source']['schema:deathDate']);
        } elseif (isset($this->fields['_source']['dbp:deathYear'])) {
            return $this->parseDate($this->fields['_source']['dbp:deathYear']);
        } else {
            return "no content provided";
        }
    }

    public function getAlternativeNames()
    {
        return $this->getValueIfAvailable('schema:alternateName');
    }

    public function getThumbnail()
    {
        return $this->getValueIfAvailable('dbp:thumbnail', "../themes/linkedswissbib/images/personAvatar.png");
    }

    public function getSource()
    {
        return $this->getValueIfAvailable('owl:sameAs');
    }

    public function getName()
    {
        return $this->getValueIfAvailable('dct:contributor');
    }

    public function isPerson()
    {
        return $this->getDataType() == "person" ? true : false;
    }

    public function isSubject()
    {
        return $this->getDataType() == "DEFAULT" ? true : false; //TODO: this could be problematic, if another entitytype uses DEFAULT as well.
    }

    public function isInstance()
    {
        return $this->getDataType() == "bibliographicResource" ? true : false;
    }

    public function getDataType()
    {
        return $this->fields['_type'];
    }

    /* Currently no properties
    public function getAlternativeTitle()
    {
        $array = $this->fields['_source']['dct:alternative'];

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
    */

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

    /* Currently no properties
    public function getOriginalLanguage()
    {
        $result = $this->fields['_source']['dbp:originalLanguage'];
        if(!isset($result)){
            return "No content";
        } else {
            return $result;
        }
    }
    */

    public function getRdfType()
    {
        return $this->getValueIfAvailable('rdf:type');

        //what does this do:
        $rdfType = $this->fields['_source']['rdf:type'];
        $rdfType = substr_replace($rdfType, " ", 0, 30);
        return $rdfType;
    }

    /* Currently on URIs available */

    public function getCover()
    {
        if (isset($this->fields['_source']['bibo:isbn10'])) {
            $isbn10 = $this->fields['_source']['bibo:isbn10'];
            $url_start = 'https://resources.swissbib.ch/Cover/Show?isn=';
            $url_end = '&size=small';
            $link_cover = $url_start . $isbn10 . $url_end;
            return $link_cover;
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Article") {
            return "../themes/linkedswissbib/images/icon_article.png";
        } else {
            return "../themes/linkedswissbib/images/icon_no_image_available.gif";
        }
    }

    /* Currently only URIs available
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
    */

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

    public function getLanguage()
    {

        if (isset($this->fields['_source']['dct:language'])) {
            return is_array($this->fields['_source']['dct:language']) ? array_values($this->fields['_source']['dct:language']) :
                [$this->fields['_source']['dct:language']];
        } else {
            return [];
        }
    }

    public function getSubjectAlternativeNames($lang = '@value')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#variantNameForTheSubjectHeading');
    }

    public function getSubjectBroaderTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#broaderTermGeneral', '@id');
    }

    public function getSubjectDDC1($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy1', '@id');
    }

    public function getSubjectDDC2($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy2', '@id');
    }

    public function getSubjectDDC3($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy3', '@id');
    }

    public function getSubjectDDC4($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy4', '@id');
    }

    public function getSubjectDefinition($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#definition');
    }

    public function getSubjectExternalLinkSkos($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://www_w3_org/2004/02/skos/core#exactMatch', '@id');
    }

    public function getSubjectExternalLinkFoafPage($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://xmlns_com/foaf/0_1/page', '@id');
    }

    public function getSubjectGndSubjectCategory($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#gndSubjectCategory', '@id');
    }

    public function getSubjectNarrowerTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb.info/standards/elementset/gnd#narrowerTermGeneral', '@id');
    }

    public function getSubjectPreferredName($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading');
    }

    public function getSubjectRelatedTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedTerm', '@id');
    }

    //returns only dummy image
    public function getSubjectThumbnail()
    {
        return $this->getValueIfAvailable('dbp:thumbnail', "../themes/linkedswissbib/images/icon_no_image_available.gif");
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
}