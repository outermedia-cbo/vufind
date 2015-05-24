<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Sparql_Options
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Search\Sparql;

use VuFind\Search\Base\Options as BaseOptions;


class Options  extends BaseOptions
{


    /**
     * Return the route name for the search results action.
     *
     * @return string
     */
    public function getSearchAction()
    {
        return 'sparql-results';
    }




}