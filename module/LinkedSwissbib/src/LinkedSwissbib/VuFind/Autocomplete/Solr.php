<?php
 
 /**
 * [...description of the type ...]
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 12/10/13
 * Time: 11:20 AM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category swissbib_VuFind2
 * @package  [...package name...]
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */


namespace LinkedSwissbib\VuFind\Autocomplete;

use Swissbib\Vufind\Autocomplete\Solr as SwissbibAutocompleteSolr;

class Solr extends SwissbibAutocompleteSolr {


    protected function getSuggestionsFromSearch($searchResults, $query, $exact)
    {
        $results = array();
        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            array_push($results, array($current["id"], "BibRes", $current["title"][0]));
        }
        return $results;
    }


    /**
     * This method returns an array of strings matching the user's query for
     * display in the autocomplete box.
     *
     * @param string $query The user query
     *
     * @return array        The suggestions for the provided query
     */
    public function getSuggestions($query)
    {
        if (!is_object($this->searchObject)) {
            throw new \Exception('Please set configuration first.');
        }

        try {
            $this->searchObject->getParams()->setBasicSearch(
                $this->mungeQuery($query), $this->handler
            );
            $this->searchObject->getParams()->setSort($this->sortField);
            foreach ($this->filters as $current) {
                $this->searchObject->getParams()->addFilter($current);
            }

            // Perform the search:
            $searchResults = $this->searchObject->getResults();

            // Build the recommendation list -- first we'll try with exact matches;
            // if we don't get anything at all, we'll try again with a less strict
            // set of rules.
            $results = $this->getSuggestionsFromSearch($searchResults, $query, true);
            if (empty($results)) {
                $results = $this->getSuggestionsFromSearch(
                    $searchResults, $query, false
                );
            }
        } catch (\Exception $e) {
            // Ignore errors -- just return empty results if we must.
        }
        return $results; // TODO: remove duplicate entries
    }



} 