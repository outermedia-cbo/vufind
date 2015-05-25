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
use VuFindSearch\ParamBag;

class Params extends BaseParams
{

    public function getBackendParameters()
    {

        //todo: in Solr these Backend Parameters are initialized with the ones configured
        //hpw can we do this in ES?
        $backendParams = new ParamBag();

        return $backendParams;
    }


}