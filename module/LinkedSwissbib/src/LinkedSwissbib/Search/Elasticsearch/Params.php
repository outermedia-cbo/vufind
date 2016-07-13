<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Elasticsearch_Params
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Search\Elasticsearch;


use VuFind\Search\Base\Params as BaseParams;
use LinkedSwissbib\Backend\Elasticsearch\ESParamBag;

class Params extends BaseParams
{


    /**
     * Create search backend parameters for advanced features.
     *
     * @return ParamBag
     */

    public function getBackendParameters()
    {



        //todo: in Solr these Backend Parameters are initialized with the ones configured
        //hpw can we do this in ES?
        $backendParams = new ESParamBag();

        //todo: do we need spellchecking in linked?
        $backendParams->set(
            'spellcheck', $this->getOptions()->spellcheckEnabled() ? 'true' : 'false'
        );

        $facets = $this->getFacetSettings();
        if (!empty($facets)) {
            //todo: implement some kind of facet preparation for ES if we need it for linked-swissbib
        }

        // Filters
        $filters = $this->getFilterSettings();
        foreach ($filters as $filter) {
            //todo: implement filter preparation for ES if necessary
        }




        return $backendParams;
    }

    /**
     * Support method for initSearch() -- handle basic settings.
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     * request.
     *
     * @return boolean True if search settings were found, false if not.
     */
    protected function initBasicSearch($request)
    {
        // If no lookfor parameter was found, we have no search terms to
        // add to our array!
        if (is_null($lookfor = $request->get('lookfor'))) {
            return false;
        }

        // If lookfor is an array, we may be dealing with a legacy Advanced
        // Search URL.  If there's only one parameter, we can flatten it,
        // but otherwise we should treat it as an error -- no point in going
        // to great lengths for compatibility.
        if (is_array($lookfor)) {
            if (count($lookfor) > 1) {
                throw new \Exception("Unsupported search URL.");
            }
            $lookfor = $lookfor[0];
        }

        // Flatten type arrays for backward compatibility:
        $handler = $request->get('type');
        if (is_array($handler)) {
            $handler = $handler[0];
        } elseif (empty($handler)) {
            if ($request->get('method') == 'getAuthor') {
                $handler = "AuthorForId";
            } else if ($request->get('method') == 'getAuthorMulti') {
                $handler = "AuthorByIdMulti";
            } else if ($request->get('method') == 'getSubjectMulti') {
                $handler = "SubjectByIdMulti";
            } else {
                $handler = "AllFields";
            }
        }

        // Set the search:
        $this->setBasicSearch($lookfor, $handler);
        return true;
    }

    /**
     * Return current facet configurations
     *
     * @return array $facetSet
     */
    public function getFacetSettings()
    {

        //todo: do we need facets in linked-swissbib
        return [];
    }

    /**
     * Return the current filters as an array of strings ['field:filter']
     *
     * @return array $filterQuery
     */
    public function getFilterSettings()
    {
        //todo: do we need filters in linked-swissbib
        //if yes - implement preparation for ES
        return [];
    }


}