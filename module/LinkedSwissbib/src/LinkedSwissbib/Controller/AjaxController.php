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
}