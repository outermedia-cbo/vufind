<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
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
            'nested'    =>   'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Nested',
            'match' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\MatchQuery',
            'query' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Query'
        ];


    public function __construct(AbstractQuery $query, array $querySpec)
    {
        $this->query = $query;
        //$this->handler = $handler;
        $this->spec = $querySpec;
    }


    /**
     * @return array
     */
    public function build()
    {

        $clause = [];

        $queryType = $this->spec['query'];
        foreach (array_keys($queryType) as $key)
        {
            if (array_key_exists($key,$this->registeredQueryClasses))
            {

                /** @var Query $queryClass */
                $queryClass = new $this->registeredQueryClasses[$key]($this->query, $queryType[$key]);
                $queryClass->setSearchSpec($queryType[$key]);

                $clause = $queryClass->build();

                //todo: we can't use addClause in this way!
                //$this->addClause($queryClass);
            }
        }

        return $clause;

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