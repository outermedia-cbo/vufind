<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
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