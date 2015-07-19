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


    protected $registeredQueryClasses =
        ['bool' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\BooleanQuery'];




    public function __construct(AbstractQuery $query, SearchHandler $handler)
    {
        $this->query = $query;
        $this->handler = $handler;

    }


    public function build()
    {

        $queryType = $this->handler->getQuery();

        $test = "";


    }

    public function addClause(ESQueryInterface $query)
    {

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