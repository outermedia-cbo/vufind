<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/23/15
 * Time: 5:10 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder;

use VuFindSearch\Query\AbstractQuery;

interface UserQueryAwareInterface
{
    public function setUserQuery(AbstractQuery $userQuery);
    public function setSearchSpec(array $searchSpec);
    public function getUserQuery();
    public function getSearchSpec();
}