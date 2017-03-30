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

    private function getValueIfAvailable($lookup, $fallback = "Nicht bekannt")
    {
        if (isset($this->fields['_source'][$lookup]))
            $result = $this->fields['_source'][$lookup];
        else
            $result = $fallback;

        if(is_array($result))
            $result = implode(self::ARRAY_SEPARATOR,$result);

        return preg_replace(['/</','/>/'], ['&lt;','&gt;'],$result);
    }

    private function getValueForProperty($lang, $lookup, $fallback = "Nicht bekannt")
    {
        if(!isset($this->fields['_source'][$lookup]))
            return $fallback;
        $array = $this->fields['_source'][$lookup];
        return $this->getValueFromArray($lang, $array);
    }

    private function getNestedValueForProperty($lang, $lookup, $innerlookup = '@value', $fallback = "Nicht bekannt")
    {
        $result = $this->getValueForProperty($lang, $lookup, $fallback);

        if(is_array($result)) {
            return $result[$innerlookup];
        }
        return $result;
    }

    // Some properties are available in several languages. The idea is to load the properties in the language of the Website. At the moment, German is set as default language (see below)
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
                    return "Nicht bekannt";
                }
            }
            return implode(self::ARRAY_SEPARATOR, $result);
        }
    }

    private function parseDate($date)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return date("d.m.Y", strtotime($date));
        } elseif (preg_match("/^[0-9]{4}$/", $date)) {
            return $date;
        }
        return "could not parse date!";
    }

    private function parseYear($date)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return date("Y", strtotime($date));
        } elseif (preg_match("/^[0-9]{4}$/", $date)) {
            return $date;
        }
        return "could not parse date!";
    }

    private function addHtmlToExternalLinks ($stringWithCommaSeparatedUrls)
    {
        $result = '';
        $array = explode(',', $stringWithCommaSeparatedUrls);
        foreach ($array as $link) {
            if (strpos($link, 'Nicht bekannt') === false) {
                $result .= '<a target="_blank" href="' . $link . '"><i class="fa fa-external-link"></i> ' . $link . '</a></br>';
            }
        }
        if (!empty($result)) {
            return $result;
        } else {
            return 'Nicht bekannt';
        }
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

    public function getGenreAsLiteral($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpGenreAsLiteral');
    }

    public function getGenreAsUri()
    {
        return $this->getValueIfAvailable('dbp:genre');
    }

    public function getInfluenced($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpInfluenced');
    }

    public function getInfluencedBy($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpInfluencedBy');
    }

    public function getMovementAsLiteral($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpMovementAsLiteral');
    }

    public function getMovementAsUri()
    {
        return $this->getValueIfAvailable('dbp:movement');
    }

    public function getBiography($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'dbp:abstract');
    }

    public function getBirthPlace($lang = 'de')
    {
        return $result = $this->getValueForProperty($lang, 'lsb:dbpBirthPlaceAsLiteral');
    }

    public function getNameAsString()
    {
        //not ideal solution since it matches first and last name that do not actually belong together
        if(isset($this->fields['_source']['foaf:firstName']) && isset($this->fields['_source']['foaf:lastName'])){
            $firstName = is_array($this->fields['_source']['foaf:firstName']) ? $this->fields['_source']['foaf:firstName'][0] : $this->fields['_source']['foaf:firstName'];
            $lastName = is_array($this->fields['_source']['foaf:lastName']) ? $this->fields['_source']['foaf:lastName'][0] : $this->fields['_source']['foaf:lastName'];
            return $firstName . " " . $lastName;
        } elseif(isset($this->fields['_source']['foaf:lastName'])) {
            return is_array($this->fields['_source']['foaf:lastName']) ? $this->fields['_source']['foaf:lastName'][0] : $this->fields['_source']['foaf:lastName'];
        } else {
            return $this->getValueIfAvailable('foaf:name');
        }
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
            return "Nicht bekannt";
        }
    }

    public function getBirthYear()
    {
        if (isset($this->fields['_source']['dbp:birthDate'])) {
            return $this->parseYear($this->fields['_source']['dbp:birthDate']);
        } elseif (isset($this->fields['_source']['schema:birthDate'])) {
            return $this->parseYear($this->fields['_source']['schema:birthDate']);
        } elseif (isset($this->fields['_source']['dbp:birthYear'])) {
            return $this->parseYear($this->fields['_source']['dbp:birthYear']);
        } else {
            return "Nicht bekannt";
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
            return "Nicht bekannt";
        }
    }

    public function getDeathYear()
    {
        if (isset($this->fields['_source']['dbp:deathDate'])) {
            return $this->parseYear($this->fields['_source']['dbp:deathDate']);
        } elseif (isset($this->fields['_source']['schema:deathDate'])) {
            return $this->parseYear($this->fields['_source']['schema:deathDate']);
        } elseif (isset($this->fields['_source']['dbp:deathYear'])) {
            return $this->parseYear($this->fields['_source']['dbp:deathYear']);
        } else {
            return "Nicht bekannt";
        }
    }

    public function getBirthAndDeathYear()
    {
        $fallback = "Nicht bekannt";
        $birthYear = $this->getBirthYear();
        $deathYear = $this->getDeathYear();
        if (($birthYear != $fallback) && ($deathYear != $fallback)) {
            return ' (' . $birthYear . ' - ' . $deathYear. ')';
        } elseif (($birthYear != $fallback) && ($deathYear = $fallback)) {
            return ' (' . $birthYear . ' - ?)';
        } elseif (($birthYear = $fallback) && ($deathYear != $fallback)) {
            return ' (? ' . $deathYear . ')';
        } elseif (($birthYear = $fallback) && ($deathYear = $fallback)) {
            return '';
        } else {
            return '';
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

    public function getPersonExternalLinks()
    {
        $owl = $this->getValueIfAvailable('owl:sameAs');
        $schema = $this->getValueIfAvailable('schema:sameAs');
        $links = $owl . ", " . $schema;
        return $this->addHtmlToExternalLinks ($links);
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

    public function getID()
    {
        return $this->fields['_id'];
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

    public function getRdfType()
    {
        return $this->getValueIfAvailable('rdf:type');
    }

    public function getRdfTypeTrimmed()
    {
        $rdfType = $this->getValueIfAvailable('rdf:type');
        return $this->getTrimmedString("http://purl.org/ontology/bibo/", $rdfType);
    }

    public function getTrimmedString($search, $subject)
    {
        return str_replace($search, "", $subject);
    }

    /* Currently as URIs available */

    public function getCover()
    {
        if (isset($this->fields['_source']['bibo:isbn10'])) {
            $isbn10 = $this->fields['_source']['bibo:isbn10'];
            $url_start = 'https://www.swissbib.ch/Cover/Show?isn=';
            $url_end = '&size=small';
            $link_cover = $url_start . $isbn10 . $url_end;
            return $link_cover;
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Article") {
            return "../themes/linkedswissbib/images/mediaicons/5_Artikel.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Book") {
            return "../themes/linkedswissbib/images/mediaicons/7_Buch.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Manuscript") {
            return "../themes/linkedswissbib/images/mediaicons/28_Handschrift.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Periodical") {
            return "../themes/linkedswissbib/images/mediaicons/9_Zeitschrift.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Series") {
            return "../themes/linkedswissbib/images/mediaicons/7_Buch.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Thesis") {
            return "../themes/linkedswissbib/images/mediaicons/7_Buch.png";
        } elseif (isset($this->fields['_source']['rdf:type']) && $this->fields['_source']['rdf:type'] == "http://purl.org/ontology/bibo/Website") {
            return "../themes/linkedswissbib/images/mediaicons/25_Online_Ressourcen.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1023") {
            return "../themes/linkedswissbib/images/mediaicons/19_Film.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1020") {
            return "../themes/linkedswissbib/images/mediaicons/1_Dummy_Buch.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1021") {
            return "../themes/linkedswissbib/images/mediaicons/24_Objekt_Spiel.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1014") {
            return "../themes/linkedswissbib/images/mediaicons/23_Bildmaterial.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1007") {
            return "../themes/linkedswissbib/images/mediaicons/26_Software_auf_Datentraeger.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1002") {
            return "../themes/linkedswissbib/images/mediaicons/21_Kartenmaterial.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1011") {
            return "../themes/linkedswissbib/images/mediaicons/13_Musik.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1010") {
            return "../themes/linkedswissbib/images/mediaicons/17_Notenmaterial.png";
        } elseif (isset($this->fields['_source']['rdau:P60049']) && $this->fields['_source']['rdau:P60049'] == "http://rdvocab.info/termList/RDAContentType/1006") {
            return "../themes/linkedswissbib/images/mediaicons/21_Kartenmaterial.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1007") {
            return "../themes/linkedswissbib/images/mediaicons/23_Bildmaterial.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1002") {
            return "../themes/linkedswissbib/images/mediaicons/27_Microfilm.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1003") {
            return "../themes/linkedswissbib/images/mediaicons/26_Software_auf_Datentraeger.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1001") {
            return "../themes/linkedswissbib/images/mediaicons/13_Musik.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1005") {
            return "../themes/linkedswissbib/images/mediaicons/19_Film.png";
        } elseif (isset($this->fields['_source']['rdau:P60050']) && $this->fields['_source']['rdau:P60050'] == "http://rdvocab.info/termList/RDAMediaType/1008") {
            return "../themes/linkedswissbib/images/mediaicons/18_Film_online.png";
        } else {
            return "../themes/linkedswissbib/images/icon_no_image_available.gif";
        }
    }

    /* independent from given language (Website language) */
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

    public function getSubjectUriForBroaderTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#broaderTermGeneral', '@id');
    }

    public function getSubjectDDC($lang = 'de')
    {
        $ddc1 = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy1', '@id');
        $ddc2 = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy2', '@id');
        $ddc3 = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy3', '@id');
        $ddc4 = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedDdcWithDegreeOfDeterminacy4', '@id');
        $links = $ddc1 . ', ' .  $ddc2 . ', ' . $ddc3 . ', ' . $ddc4;
        return $this->addHtmlToExternalLinks ($links);
    }

    public function getSubjectDefinition($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#definition');
    }

    public function getSubjectExternalLinks ($lang = 'de') {
        $gnd =  $this->getValueIfAvailable('@id');
        $skos = $this->getNestedValueForProperty($lang, 'http://www_w3_org/2004/02/skos/core#exactMatch', '@id');
        $foaf = $this->getNestedValueForProperty($lang, 'http://xmlns_com/foaf/0_1/page', '@id');
        $links = $gnd . ', ' .  $skos . ', ' . $foaf;
        return $this->addHtmlToExternalLinks ($links);
    }

    public function getSubjectGndSubjectCategory($lang = 'de')
    {
        $links = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#gndSubjectCategory', '@id');
        return $this->addHtmlToExternalLinks ($links);
    }

    public function getSubjectUriForNarrowerTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb.info/standards/elementset/gnd#narrowerTermGeneral', '@id');
    }

    public function getSubjectPreferredName($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading');
    }

    public function getSubjectUriForRelatedTerms($lang = 'de')
    {
        return $result = $this->getNestedValueForProperty($lang, 'http://d-nb_info/standards/elementset/gnd#relatedTerm', '@id');
    }

    //returns always dummy image
    public function getSubjectThumbnail()
    {
        return $this->getValueIfAvailable('dbp:thumbnail', "../themes/linkedswissbib/images/subjectAvatar.png");
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
