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

use LinkedSwissbib\Backend\Elasticsearch\ESParamBag;
use VuFindSearch\Response\RecordCollectionInterface;
use VuFindSearch\Backend\Exception\BackendException;
use LinkedSwissbib\esQuery;

class Backend extends AbstractBackend
{


    /**
     * @var ESQueryBuilder
     */
    protected $queryBuilder;


    /**
     * @var \LinkedSwissbib\Backend\Elasticsearch\Connector
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
        if (isset($params) && !$params instanceof ESParamBag )
        {
            throw new \Exception ("invalid ParamBag type for ElasticSearch target");
        }

        //Todo;
        //at the moment I'm not sure how to use the ParamBag and QueryBuilder Type
        //are we going to do it in the same way as it is done in SOLR (where Param bag creates the list of key-value parameters for the
        //HTTP-Get query or is the QueryBuilder type responsible for the creation of the ES DSL specific structure
        //which is at the end a PHP array
        $params = $params ?: new ESParamBag();
        //$this->injectResponseWriter($params); SOLR Stuff

        //$params->set('rows', $limit);
        //$params->set('start', $offset);

        $this->getQueryBuilder()->setParams($params);
        $esDSLParams = $this->getQueryBuilder()->build($query);



        $response   = $this->connector->search($esDSLParams);

        //todo: fetch the Metadadata for this search

        /*

        foreach ($response['hits']['hits'] as $hit) {

            $source = $hit['_source'];
            $rdfJson = json_encode($source);
            $rdfGraph = new \EasyRdf_Graph();
            $result = $rdfGraph->parse($rdfJson,'jsonld');

            $turtle = $rdfGraph->serialise('turtle');
            $ntriples = $rdfGraph->serialise('ntriples');
            $jsonld = $rdfGraph->serialise('jsonld');

            //todo: create structures and serialize them in RecordDrivers which are used in the view component
            $t = "";


        }
        */

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