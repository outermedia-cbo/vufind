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


use VuFindSearch\Backend\AbstractBackend;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Response\RecordCollectionFactoryInterface;

class Backend extends AbstractBackend
{


    /**
     * @var ESQueryBuilder
     */
    protected $queryBuilder;


    /**
     * Return the record collection factory.
     *
     * Lazy loads a generic collection factory.
     *
     * @return RecordCollectionFactoryInterface
     */
    public function getRecordCollectionFactory()
    {
        // TODO: Implement getRecordCollectionFactory() method.
    }


    /**
     * Perform a search and return record collection.
     *
     * @param AbstractQuery $query Search query
     * @param integer $offset Search offset
     * @param integer $limit Search limit
     * @param ParamBag $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function search(AbstractQuery $query, $offset, $limit,
                           ParamBag $params = null
    )
    {
        // TODO: Implement search() method.
    }

    /**
     * Retrieve a single document.
     *
     * @param string $id Document identifier
     * @param ParamBag $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function retrieve($id, ParamBag $params = null)
    {
        // TODO: Implement retrieve() method.
    }

    /**
     * Return query builder.
     *
     * Lazy loads an empty default QueryBuilder if none was set.
     *
     * @return ESQueryBuilder
     */
    public function getQueryBuilder()
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = new ESQueryBuilder();
        }
        return $this->queryBuilder;
    }

    /**
     * Set the query builder.
     *
     * @param ESQueryBuilder $queryBuilder
     *
     * @return void
     */
    public function setQueryBuilder(ESQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }


    /**
     * Create the query builder.
     *
     * @return ESQueryBuilder
     */
    protected function createQueryBuilder()
    {

        /*

        todo : do we need this?
        $specs   = $this->loadSpecs();
        $config = $this->config->get('config');
        $defaultDismax = isset($config->Index->default_dismax_handler)
            ? $config->Index->default_dismax_handler : 'dismax';
        */
        $builder = new ESQueryBuilder();

        // Configure builder:
        //todo: configure ES Builder if necessary
        /*
        $search = $this->config->get($this->searchConfig);
        $caseSensitiveBooleans
            = isset($search->General->case_sensitive_bools)
            ? $search->General->case_sensitive_bools : true;
        $caseSensitiveRanges
            = isset($search->General->case_sensitive_ranges)
            ? $search->General->case_sensitive_ranges : true;
        $helper = new LuceneSyntaxHelper(
            $caseSensitiveBooleans, $caseSensitiveRanges
        );
        $builder->setLuceneHelper($helper);

        */
        return $builder;
    }



}