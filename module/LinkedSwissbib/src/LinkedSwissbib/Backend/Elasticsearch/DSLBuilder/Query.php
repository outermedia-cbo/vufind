<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/19/15
 * Time: 9:02 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder;


use VuFindSearch\Query\AbstractQuery;
use LinkedSwissbib\Backend\Elasticsearch\SearchHandler;

class Query implements ESQueryInterface
{

    protected $limit;
    protected $size;

    protected $query;

    /**
     * @var SearchHandler
     */
    protected $handler;
    protected $spec;
    protected $clauses  = [];


    //todo: think about a plugin manager solution
    protected $registeredQueryClasses =
        [
            'bool' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\BooleanQuery',
            'multi_match' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\MultiMatchQuery',
            'nested'    => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\NestedQuery',
            'match' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\MatchQuery'
        ];




    public function __construct(AbstractQuery $query, array $querySpec)
    {
        $this->query = $query;
        //$this->handler = $handler;
        $this->spec = $querySpec;

    }


    public function build()
    {

        $queryType = $this->spec['query'];
        foreach (array_keys($queryType) as $key)
        {
            if (array_key_exists($key,$this->registeredQueryClasses))
            {
                $queryClass = new $this->registeredQueryClasses[$key]($this->query, $queryType[$key]);
                $this->addClause($queryClass);
            }
        }

        $test = "";


    }

    public function addClause(ESQueryInterface $query)
    {
        $this->clauses[] = $query;
    }

    public function getClause($name)
    {

    }

    public function removeClause($name)
    {

    }

    public function addSpec(array $spec)
    {

    }

}