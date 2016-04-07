<?php

namespace LinkedSwissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;



class AjaxController extends VFAjaxController {

    /**
     *
     */
    protected function getAuthorAjax() {
        $this->writeSession();
        $query = $this->getRequest()->getQuery();
        $content = array('foo' => $query);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
        $response->setContent(json_encode($content));
        return $response;
    }
}