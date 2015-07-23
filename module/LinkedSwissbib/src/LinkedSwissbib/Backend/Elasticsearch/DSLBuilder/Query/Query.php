<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/19/15
 * Time: 9:02 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query;


use VuFindSearch\Query\AbstractQuery;
use LinkedSwissbib\Backend\Elasticsearch\SearchHandler;

class Query implements ESQueryInterface
{

    protected $limit;
    protected $size;

    protected $userQuery;

    /**
     * @var SearchHandler
     */
    protected $handler;
    protected $spec;
    protected $clauses  = [];


    //todo: think about a plugin manager solution
    protected $registeredQueryClasses =
        [
            'bool' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\BooleanQuery',
            'multi_match' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\MultiMatchQuery',
            'nested'    => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\NestedQuery',
            'match' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\MatchQuery'
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
                //todo: we can't use addClause in this way!
                //$this->addClause($queryClass);
            }
        }

    }

    public function getName()
    {
        return  get_class($this);
    }

    public function setUserQuery(AbstractQuery $userQuery)
    {
        $this->query = $userQuery;
    }

    public function setSearchSpec(array $searchSpec)
    {
        $this->spec = $searchSpec;
    }

    public function getUserQuery()
    {
        return $this->query;
    }

    public function getSearchSpec()
    {
        return $this->spec;
    }


}