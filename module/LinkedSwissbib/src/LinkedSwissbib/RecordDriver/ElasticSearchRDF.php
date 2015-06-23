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
        throw new \Exception(__FUNCTION__ . ' currently not supported');
    }


    public function getRDF($rdfType = 'turtle')
    {

        $source = $this->fields['_source'];
        $rdfJson = json_encode($source);
        $rdfGraph = new \EasyRdf_Graph();
        $result = $rdfGraph->parse($rdfJson,'jsonld');



        //foreach ($rdfGraph as $subject) {
        //    $test = "";
        //}
        $myResource = $rdfGraph->resources();
        $php = $rdfGraph->toRdfPhp();
        $turtle = $rdfGraph->serialise($rdfType);
        //$graph2 = new \EasyRdf_Graph();
        //$graph2->parse($turtle,'turtle');

        //$language = $graph2->getResource('http://data.swissbib.ch/resource/212817795','dc:language');

        //$properties = $graph2->properties('http://data.swissbib.ch/resource/212817795');
        //foreach($properties as $property) {

        //    $wasistDas = $graph2->get('http://data.swissbib.ch/resource/212817795',$property);
        //    $dd = "";

        //}
        $citation = $rdfGraph->getLiteral('http://data.swissbib.ch/resource/212817795','dc:bibliographicCitation');
        $languageFromGraph = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','dc:language');
        //$uri = $rdfGraph->allResources('http://data.swissbib.ch/resource/212817795','dc:bibliographicCitation');
        //return $rdfGraph->dump();
        //$hello = \EasyRdf_Graph::newAndLoad("http://www.dbpedialite.org/things/10");
        //$AA = $rdfGraph->primaryTopic();
        $triples = $rdfGraph->countTriples();
        $type = $rdfGraph->type();
        //$rdfGraph->
        //$t3 = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','rdau:placeOfPublication');
        $t3 = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','ns0:placeOfPublication');
        $t = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','rdfs:isDefinedBy');
        $t1 = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','dct:title');
        $t2 = $rdfGraph->getLiteral('http://data.swissbib.ch/resource/212817795','dct:title');
        //$t3 = $rdfGraph->getResource('http://data.swissbib.ch/resource/212817795','rdau:placeOfPublication');
        //$t = $rdfGraph
        //todo: evaluate response type - parse error?
        return $rdfGraph->serialise($rdfType);
        //$ntriples = $rdfGraph->serialise('ntriples');
        //$jsonld = $rdfGraph->serialise('jsonld');


    }



}