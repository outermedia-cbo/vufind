<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 5/24/15
 * Time: 12:47 PM
 */

namespace LinkedSwissbib\src\LinkedSwissbib\Backend\Elasticsearch;


use VuFindSearch\Backend\AbstractBackend;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Response\RecordCollectionFactoryInterface;

class Backend extends AbstractBackend
{

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
}