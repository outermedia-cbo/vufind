<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/17/15
 * Time: 5:36 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder;


interface ESQueryInterface
{
    public function build();
    public function addClause(ESQueryInterface $query);
    public function getClause($name);
    public function removeClause($name);

}