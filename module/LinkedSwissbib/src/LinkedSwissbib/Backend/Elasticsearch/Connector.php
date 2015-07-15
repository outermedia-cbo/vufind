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

use Elasticsearch\ClientBuilder;
use VuFindSearch\Backend\Exception\BackendException;
use VuFindSearch\ParamBag;
use Elasticsearch\Client;


class Connector implements \Zend\Log\LoggerAwareInterface
{
    use \VuFind\Log\LoggerAwareTrait;

    /** @var array $indexConfig*/
    private  $indexConfig;

    public function __construct(array $config) {
        $this->indexConfig = $config;
    }


    /**
     * @var
     */
    private $proxy;

    protected $adapter = 'Zend\Http\Client\Adapter\Socket';


    /**
     * Set the HTTP proxy service.
     *
     * @param mixed $proxy Proxy service
     *
     * @return void
     *
     * @todo Typehint on ProxyInterface
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Execute a search.
     *
     * @param ParamBag $params Parameters
     *
     * @return string
     */
    public function search(array $params)
    {

        $client = $this->createClient();
        return $this->send($client,$params);

        //$handler = $this->map->getHandler(__FUNCTION__);
        //$this->map->prepare(__FUNCTION__, $params);
        //return $this->query($handler, $params);
    }


    /**
     * Send query to SOLR and return response body.
     *
     * @param string   $handler SOLR request handler to use
     * @param ParamBag $params  Request parameters
     *
     * @return string Response body
     */
    public function query($handler, ParamBag $params)
    {

        //todo: how to use this in ES? do we need it?

        $url         = $this->url . '/' . $handler;
        $paramString = implode('&', $params->request());
        if (strlen($paramString) > self::MAX_GET_URL_LENGTH) {
            $method = Request::METHOD_POST;
        } else {
            $method = Request::METHOD_GET;
        }

        if ($method === Request::METHOD_POST) {
            $client = $this->createClient($url, $method);
            $client->setRawBody($paramString);
            $client->setEncType(HttpClient::ENC_URLENCODED);
            $client->setHeaders(['Content-Length' => strlen($paramString)]);
        } else {
            $url = $url . '?' . $paramString;
            $client = $this->createClient($url);
        }

        $this->debug(sprintf('Query %s', $paramString));
        //return $this->send($client);
    }

    /**
     * Send request the SOLR and return the response.
     *
     * @param HttpClient $client Prepare HTTP client
     *
     * @return string Response body
     *
     * @throws RemoteErrorException  SOLR signaled a server error (HTTP 5xx)
     * @throws RequestErrorException SOLR signaled a client error (HTTP 4xx)
     */
    protected function send(Client $client,$params)
    {
        /*
        $this->debug(
            sprintf('=> %s %s', $client->getMethod(), $client->getUri())
        );
        */

        $time     = microtime(true);
        $response =  $client->search($params);
        $time     = microtime(true) - $time;

        /*
        $this->debug(
            sprintf(
                '<= %s %s', $response->getStatusCode(),
                $response->getReasonPhrase()
            ), ['time' => $time]
        );

        if (!$response->isSuccess()) {
            throw HttpErrorException::createFromResponse($response);
        }
        */
        //todo: return only the body
        return $response;
    }

    /**
     * Create the HTTP client.
     *
     * @param string $url    Target URL
     *
     * @return Client
     */
    protected function createClient($url = null)
    {
        //todo: configure the ES cluster

        if (!isset($this->indexConfig['hosts'])) {
            throw new BackendException("target hosts are not configured");
        }

        $client = ClientBuilder::create()->setHosts($this->indexConfig['hosts'])->build();

        return $client;
    }


}