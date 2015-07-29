<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Backend\Elasticsearch;

use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\ParamBag;


//Todo: There should be a general QueryBuilderInterface in VuFindSearch for all the targets in VuFind
interface ESQueryBuilderInterface {


    /**
     * Build build a query for the target based on VuFind query object.
     *
     * @param AbstractQuery $query Query object
     *
     * @return ParamBag
     */
    public function build(AbstractQuery $query);

    //todo: we should use an interface - actually not implemented by VuFind
    public function setParams (ESParamBag $paramsBag);



}