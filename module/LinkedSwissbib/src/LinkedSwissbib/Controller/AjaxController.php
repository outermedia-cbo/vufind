<?php

namespace LinkedSwissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;



class AjaxController extends VFAjaxController {

    /**
     * Handles passing data to the class
     *
     * @return mixed
     */
    public function jsonAction() {

        $searcher = $this->params()->fromQuery('searcher');

        if ($searcher == "Solr") {
            return parent::jsonAction();
        } else {
            // Set the output mode to JSON:
            $this->outputMode = 'json';

            // Call the method specified by the 'method' parameter; append Ajax to
            // the end to avoid access to arbitrary inappropriate methods.
            $callback = [$this, $this->params()->fromQuery('method') . 'Ajax'];
            if (is_callable($callback)) {
                try {
                    return call_user_func($callback);
                } catch (\Exception $e) {
                    $debugMsg = ('development' == APPLICATION_ENV)
                        ? ': ' . $e->getMessage() : '';
                    return $this->output(
                        $this->translate('An error has occurred') . $debugMsg,
                        self::STATUS_ERROR
                    );
                }
            } else {
                return $this->output(
                    $this->translate('Invalid Method'), self::STATUS_ERROR
                );
            }
        }
    }

    /**
     * Get Autocomplete suggestions.
     *
     * @return \Zend\Http\Response
     */
    protected function getACSuggestionsAjax()
    {
        $this->writeSession();  // avoid session write timing bug
        $query = $this->getRequest()->getQuery();
        $autocompleteManager = $this->getServiceLocator()
            ->get('VuFind\AutocompletePluginManager'); // TODO: check if this is correct
        return $this->output(
            $autocompleteManager->getSuggestions($query), self::STATUS_OK
        );
    }

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