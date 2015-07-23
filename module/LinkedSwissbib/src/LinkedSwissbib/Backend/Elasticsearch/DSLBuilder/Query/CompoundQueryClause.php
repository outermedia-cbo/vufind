<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/23/15
 * Time: 5:26 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query;



abstract class CompoundQueryClause
{

    protected $clauses = [];


    public function addClause(Query $clauseQuery)
    {
        $this->clauses[] = $clauseQuery;
    }

    public function getClause()
    {
        //todo: do we need this?
        throw new \Exception ('method ' . __FUNCTION__ . 'not implemented so far');
    }


    public function removeClause ()
    {
        //todo: do we need this?
        throw new \Exception('method ' . __FUNCTION__ . 'not implemented so far');
    }

}