<?php

/**
 *
 * @category linked-swissbib
 * @package  Search_Factory
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */
namespace LinkedSwissbib\Search\Factory;


//use LinkedSwissbib\Backend\Elasticsearch\ESQueryBuilder;
use ElasticsearchAdapter\ESQueryBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LinkedSwissbib\Backend\Elasticsearch\Backend;
//use LinkedSwissbib\Backend\Elasticsearch\Connector;
use ElasticsearchAdapter\Connector;

use LinkedSwissbib\Backend\Elasticsearch\Response\RecordCollectionFactory;

class ElasticSearchBackendFactory implements FactoryInterface
{

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    private $serviceLocator;

    private $config;

    private $logger;

    private $defaultESIndex = ['hosts' => ['sb-s2.swissbib.unibas.ch:8080','sb-s6.swissbib.unibas.ch:8080','sb-s7.swissbib.unibas.ch:8080']];



    /**
     * Search configuration file identifier.createQueryBuilder
     *
     * @var string
     */
    protected $searchConfig;
    protected $searchYaml;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchConfig = 'searches';
        $this->searchYaml = 'searchspecsES.yaml';
    }



    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config         = $this->serviceLocator->get('VuFind\Config');

        if ($this->serviceLocator->has('VuFind\Logger')) {
            $this->logger = $this->serviceLocator->get('VuFind\Logger');
        }
        $connector = $this->createConnector();
        $backend   = $this->createBackend($connector);
        $this->createListeners($backend);
        return $backend;
    }


    /**
     * Create the ElasticSearch connector.
     *
     * @return Connector
     */
    protected function createConnector()
    {


        //For the Solr Backend there are e.g. additional Handlers and other stuff
        //we have to evaluate if we need this in a similar way

        /**  @var \Zend\Config\Config $zendConfig */
        $zendConfig = $this->config->get("config");

        $connector =   isset($zendConfig->LinkedIndex) && isset($zendConfig->LinkedIndex->hosts) ?
            new Connector($zendConfig->LinkedIndex->toArray()) :  new Connector($this->defaultESIndex);

        if ($this->serviceLocator->has('VuFind\Http')) {
            //do we want this Proxy??
            //$connector->setProxy($this->serviceLocator->get('VuFind\Http'));
        }
        return $connector;
    }

    /**
     * Create the Elasticsearch backend.
     *
     * @param Connector $connector Connector
     *
     * @return Backend
     */
    protected function createBackend(Connector $connector)
    {
        $backend = new Backend($connector);
        $backend->setQueryBuilder($this->createQueryBuilder());
        if ($this->logger) {
            $backend->setLogger($this->logger);
        }

        $manager = $this->serviceLocator->get('LinkedSwissbib\RecordDriverPluginManager');
        $factory = new RecordCollectionFactory(array($manager, 'getElasticSearchRecord'));
        $backend->setRecordCollectionFactory($factory);

        return $backend;
    }


    /**
     * Create the query builder.
     *
     * @return ESQueryBuilder
     */
    protected function createQueryBuilder()
    {
        /*
         * these functions are doubled (also in Solr - Refactoring advisable)
        $specs   = $this->loadSpecs();
        $config = $this->config->get('config');
        $defaultDismax = isset($config->Index->default_dismax_handler)
            ? $config->Index->default_dismax_handler : 'dismax';

        */
        $specs   = $this->loadSpecs();
        $builder = new ESQueryBuilder($specs);


        // Configure builder:
        /**
         *

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


    /**
     * Create listeners.
     *
     * @param Backend $backend Backend
     *
     * @return void
     */
    protected function createListeners(Backend $backend)
    {

        //todo look up listeners creation in SOLR
        //do we need similar in ES?

    }

    /**
     * Load the search specs.
     *
     * @return array
     */
    protected function loadSpecs()
    {
        $specReader = $this->serviceLocator->get('VuFind\SearchSpecsReader');
        return $specReader->get($this->searchYaml);
    }








}