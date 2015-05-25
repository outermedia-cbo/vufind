<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/25/15
 * Time: 10:04 AM
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


}