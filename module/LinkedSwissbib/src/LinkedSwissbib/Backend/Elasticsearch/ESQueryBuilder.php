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
use VuFindSearch\Query\Query as VuFindQuery;
use LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Query;


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
     * Indexes the search is executed on
     * @var array
     */
    //todo: make it configurable
    protected $searchIndexes = ['testsb'];




    /**
     * Constructor.
     * @param array  $specs                Search handler specifications
     * @param string $defaultDismaxHandler Default dismax handler (if no
     * DismaxHandler set in specs).
     *
     */
    public function __construct(array $specs = [],
                                $defaultDismaxHandler = 'dismax'
    ) {
        $this->defaultDismaxHandler = $defaultDismaxHandler;
        $this->setSpecs($specs);
    }




    /**
     * Build build a query for the target based on VuFind query object.
     * @param AbstractQuery $query Query object
     * @return ParamBag
     */
    public function build(AbstractQuery $vuFindQuery)
    {


        /** @var SearchHandler $searchHandlerType */
        if ($vuFindQuery instanceof VuFindQuery) {
            $searchHandlerType = $this->getSearchHandler($vuFindQuery->getHandler());
        } else {
            $searchHandlerType = $this->getSearchHandler('allfields');
        }

        $esQuery = new Query($vuFindQuery,$searchHandlerType->getSpec());
        $searchBody =  $esQuery->build();

        $getParams['body']['query'] = $searchBody;
        $getParams['type'] = $searchHandlerType->getTypes();
        $getParams['index'] = $this->searchIndexes;


        return $getParams;
    }

    public function setParams(ESParamBag $paramsBag)
    {
        $this->params = $paramsBag;
    }

    /**
     * Set query builder search specs.
     * @param array $specs Search specs
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


    /**
     * @return SearchHandler
     */
    protected function getSearchHandler($queryHandlerString)
    {
        return  array_key_exists(strtolower($queryHandlerString),$this->specs) ? $this->specs[strtolower($queryHandlerString)] :
            $this->specs['allfields'];
    }


}