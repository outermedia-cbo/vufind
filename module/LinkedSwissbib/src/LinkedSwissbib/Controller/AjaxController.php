<?php

namespace LinkedSwissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;



class AjaxController extends VFAjaxController {

    public function jsonAction() {

        $searcher = $this->params()->fromQuery('searcher');

        if ($searcher == "Solr") {
            return parent::jsonAction();
        } else {
            // https://arjunphp.com/return-json-response-zend-framework-2/
            $content = array( 'foo' => 'bar' );
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
            $response->setContent(json_encode($content));
            return $response;
        }
    }
}