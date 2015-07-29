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


class ESParamBag extends ParamBag
{

    /**
     * Return ES DSL specific array of params ready to be used in a HTTP request.
     *
     * @return array
     */
    public function request()
    {
        $request = [];
        foreach ($this->params as $name => $values) {
            if (!empty($values)) {
                $request = array_merge(
                    $request,
                    array_map(
                        function ($value) use ($name) {
                            return sprintf(
                                '%s=%s', urlencode($name), urlencode($value)
                            );
                        },
                        $values
                    )
                );
            }
        }
        return $request;
    }



}