<?php

namespace LinkedSwissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;



class AjaxController extends VFAjaxController {

    /**
     *
     */
    protected function getAjax() {
        $this->disableSessionWrites();

        $manager = $this->getServiceLocator()->get('LinkedSwissbib\SearchResultsPluginManager');

        $results = $manager->get('ElasticSearch');
        $params = $results->getParams();

        // Send both GET and POST variables to search class:
        $params->initFromRequest(
            new \Zend\Stdlib\Parameters(
                $this->getRequest()->getQuery()->toArray()
                + $this->getRequest()->getPost()->toArray()
            )
        );

        $results->performAndProcessSearch();

        // access GET parameters
        //$q = $this->params()->fromQuery('q');
        //$query = $this->getRequest()->getQuery();

        $content = $this->refine($results->getResults());

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
        $response->getHeaders()->addHeaderLine( 'Access-Control-Allow-Origin', '*' );

        $response->setContent(json_encode($content));
        return $response;
    }

    protected function getAuthorMultiAjax() {
        return $this->getAjax();
    }

    protected function getSubjectMultiAjax() {
        return $this->getAjax();
    }

    private function refine($searchResults) {
        $results = array();

        // gather results by _type
        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            $type = $current['_type'];

            $results[$type][] = $current;
        }

        return $results;
    }

    /**
     * Send output data and exit.
     *
     * @param mixed  $data     The response data
     * @param string $status   Status of the request
     * @param int    $httpCode A custom HTTP Status Code
     *
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    protected function output($data, $status, $httpCode = null)
    {
        if ($this->outputMode == 'json') {
            $response = $this->getResponse();
            $headers = $response->getHeaders();
            $headers->addHeaderLine(
                'Content-type', 'application/json'
            );
            $headers->addHeaderLine(
                'Cache-Control', 'no-cache, must-revalidate'
            );
            $headers->addHeaderLine(
                'Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'
            );
            $output = ['data' => $data,'status' => $status];
            if ('development' == APPLICATION_ENV && count(self::$php_errors) > 0) {
                $output['php_errors'] = self::$php_errors;
            }
            $response->setContent(json_encode($output));
            return $response;
        } else {
            throw new \Exception('Unsupported output mode: ' . $this->outputMode);
        }

        /*
        new code in base function
        we should evaluate if we could use this
        (together with adaptations done by Chur)
        instead of the new one


        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Cache-Control', 'no-cache, must-revalidate');
        $headers->addHeaderLine('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        if ($httpCode !== null) {
            $response->setStatusCode($httpCode);
        }
        if ($this->outputMode == 'json') {
            $headers->addHeaderLine('Content-type', 'application/javascript');
            $output = ['data' => $data, 'status' => $status];
            if ('development' == APPLICATION_ENV && count(self::$php_errors) > 0) {
                $output['php_errors'] = self::$php_errors;
            }
            $response->setContent(json_encode($output));
            return $response;
        } else if ($this->outputMode == 'plaintext') {
            $headers->addHeaderLine('Content-type', 'text/plain');
            $response->setContent($data ? $status . " $data" : $status);
            return $response;
        } else {
            throw new \Exception('Unsupported output mode: ' . $this->outputMode);
        }
        */


    }

}