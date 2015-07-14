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
     * @var ESParamBag
     */
    protected $params;

    /**
     * Search specs for exact searches.
     *
     * @var array
     */
    protected $exactSpecs = [];

    /**
     * Search specs.
     *
     * @var array
     */
    protected $specs = [];




    /**
     * Constructor.
     *
     * @param array  $specs                Search handler specifications
     * @param string $defaultDismaxHandler Default dismax handler (if no
     * DismaxHandler set in specs).
     *
     * @return void
     */
    public function __construct(array $specs = [],
                                $defaultDismaxHandler = 'dismax'
    ) {
        $this->defaultDismaxHandler = $defaultDismaxHandler;
        $this->setSpecs($specs);
    }




    /**
     * Build build a query for the target based on VuFind query object.
     *
     * @param AbstractQuery $query Query object
     *
     * @return ParamBag
     */
    public function build(AbstractQuery $query)
    {
        $getParams = [];
        if ($query) {
            $getParams['body'] = array(
                "query" => array(
                    //"match_all" => $queryAll != null ? [$queryAll] : []
                    "multi_match" => array(
                        'query' => $query->getAllTerms(),
                        'fields' => $this->specs['allfields']->getDismaxFields()
                        )
                    )
                );
            $getParams['index'] = $this->specs["allfields"]->getIndices();
            //$getParams['index'] = 'testsb';
        } else {
            $getParams['body'] = array(
                "query" => array(
                    'match_all' => [])
            );
        }

        return $getParams;
    }

    public function setParams(ESParamBag $paramsBag)
    {
        $this->params = $paramsBag;
    }

    /**
     * Set query builder search specs.
     *
     * @param array $specs Search specs
     *
     * @return void
     */
    public function setSpecs(array $specs)
    {
        foreach ($specs as $handler => $spec) {
            if (isset($spec['ExactSettings'])) {
                $this->exactSpecs[strtolower($handler)] = new SearchHandler(
                    $spec['ExactSettings'], $this->defaultDismaxHandler
                );
                unset($spec['ExactSettings']);
            }
            $this->specs[strtolower($handler)]
                = new SearchHandler($spec, $this->defaultDismaxHandler);
        }
    }


}