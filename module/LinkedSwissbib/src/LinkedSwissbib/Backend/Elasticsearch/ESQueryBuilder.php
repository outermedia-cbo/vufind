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


use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;

class ESQueryBuilder implements ESQueryBuilderInterface
{


    /**
     * Build build a query for the target based on VuFind query object.
     *
     * @param AbstractQuery $query Query object
     *
     * @return ParamBag
     */
    public function build(AbstractQuery $query)
    {
        // TODO: Implement build() method.
    }

}