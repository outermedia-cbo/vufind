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
        //todo: evaluate response type - parse error?
        return $rdfGraph->serialise($rdfType);
        //$ntriples = $rdfGraph->serialise('ntriples');
        //$jsonld = $rdfGraph->serialise('jsonld');


    }



}