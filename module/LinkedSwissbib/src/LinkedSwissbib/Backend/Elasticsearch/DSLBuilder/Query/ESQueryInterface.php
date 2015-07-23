<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/17/15
 * Time: 5:36 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query;

use LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\UserQueryAwareInterface;

interface ESQueryInterface extends UserQueryAwareInterface
{
    public function build();
    public function getName();

}