<?php
/**
 * Solr
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @category Swissbib_VuFind2
 * @package  VuFind_Autocomplete
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Autocomplete;

use VuFind\Autocomplete\Solr as VFAutocompleteSolr;

/**
 * Solr
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Solr extends VFAutocompleteSolr
{
    /**
     * GetSuggestionsFromSearch
     *
     * @param array  $searchResults SearchResults
     * @param String $query         Query
     * @param String $exact         Exact
     *
     * @return array
     */
    protected function getSuggestionsFromSearch($searchResults, $query, $exact)
    {
        $results = [];

        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            foreach ($this->displayField as $field) {
                if (isset($current[$field])) {
                    $bestMatch = $this->pickBestMatch(
                        $current[$field], $query, $exact
                    );
                    if ($bestMatch) {
                        $forbidden = [
                            ':', '&', '?', '*', '[', ']', '"', '/', '\\', ';', '.',
                            '='
                        ];
                        $bestMatch = str_replace($forbidden, " ", $bestMatch);

                        if (!$this->isDuplicate($bestMatch, $results)) {
                            $results[] = [
                                'id' => $current['id'], 'value' => $bestMatch
                            ];
                            break;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * A separate search with the original query is required to get a valid total
     *
     * @param string $query The query
     *
     * @return int
     */
    protected function getTotal($query)
    {
        $this->searchObject->getParams()->setBasicSearch(
            $query, $this->handler
        );
        foreach ($this->filters as $current) {
            $this->searchObject->getParams()->addFilter($current);
        }

        // Perform the search:
        $this->searchObject->getResults();
        $resultTotal = $this->searchObject->getResultTotal();
        $this->initSearchObject();
        return $resultTotal;
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
            $total = $this->getTotal($query);

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

        // Wrap in array as only values of result array are part of response
        $results = [
            [
                "total" => $total ?? 0,
                "suggestions" => isset($results) ? $results : []
            ]
        ];

        return $results;
    }

    /**
     * Tests if an suggestion is already in the results
     *
     * @param string $bestMatch The string to test
     * @param array  $results   The result list
     *
     * @return bool
     */
    protected function isDuplicate(string $bestMatch, array &$results)
    {
        foreach ($results as $result) {
            if ($result["value"] === $bestMatch) {
                return true;
            }
        }
        return false;
    }
}
