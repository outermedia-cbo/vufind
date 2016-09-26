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


use ElasticsearchAdapter\UserQuery\UserQuery;
use VuFindSearch\Backend\AbstractBackend;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Query\Query;
use VuFindSearch\Response\RecordCollectionFactoryInterface;

use ElasticsearchAdapter\UserQuery\ESParamBag;
use VuFindSearch\Response\RecordCollectionInterface;
use VuFindSearch\Backend\Exception\BackendException;
use ElasticsearchAdapter\ESQueryBuilder;

use ElasticsearchAdapter\Adapter as ESAdapter;
use ElasticsearchAdapter\Connector;




class Backend extends AbstractBackend
{


    /**
     * @var ESQueryBuilder
     */
    protected  $queryBuilder;


    /**
     * @var Connector
     */
    protected $connector;


    /**
     * Constructor.
     *
     * @param Connector $connector SOLR connector
     *
     * @return void
     */
    public function __construct(Connector $connector)
    {
        $this->connector    = $connector;
        $this->identifier   = null;
    }





    /**
     * Return the record collection factory.
     *
     * Lazy loads a generic collection factory.
     *
     * @return RecordCollectionFactoryInterface
     */
    public function getRecordCollectionFactory()
    {
        if (!$this->collectionFactory) {
            $this->collectionFactory = new Response\RecordCollectionFactory();
        }
        return $this->collectionFactory;
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


        $esParams = new ESParamBag();
        $esParams->exchangeArray($params->getArrayCopy());



        if (!$query instanceof Query)
            throw new \Exception("actually only single Queries are supported");


        //$params->set('rows', $limit);
        //$params->set('start', $offset);

        $esUserQuery = new UserQuery();
        $esUserQuery->setHandler($query->getHandler());
        $esUserQuery->setString($query->getString());
        $esUserQuery->setOperator($query->getOperator());

        $esAdapter = new ESAdapter($this->connector, $this->getQueryBuilder());
        $esAdapter->search($esUserQuery,$offset,$limit);


        $this->getQueryBuilder()->setParams($esParams);
        $esDSLParams = $this->getQueryBuilder()->build($esUserQuery);



        $response   = $this->connector->search($esDSLParams);

        //todo: fetch the Metadadata for this search


        $collection = $this->createRecordCollection($response);
        //$this->injectSourceIdentifier($collection);

        return $collection;
        //return  [];
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



    /// Internal API

    /**
     * Create record collection.
     *
     * @param string $json Serialized JSON response
     *
     * @return RecordCollectionInterface
     */
    protected function createRecordCollection($response)
    {
        return $this->getRecordCollectionFactory()
            ->factory($response);
    }

    /**
     * Deserialize JSON response.
     *
     * @param string $json Serialized JSON response
     *
     * @return array
     *
     * @throws BackendException Deserialization error
     */
    protected function deserialize($json)
    {
        $response = json_decode($json, true);
        $error    = json_last_error();
        if ($error != \JSON_ERROR_NONE) {
            throw new BackendException(
                sprintf('JSON decoding error: %s -- %s', $error, $json)
            );
        }
        $qtime = isset($response['responseHeader']['QTime'])
            ? $response['responseHeader']['QTime'] : 'n/a';
        $this->log('debug', 'Deserialized SOLR response', ['qtime' => $qtime]);
        return $response;
    }


}