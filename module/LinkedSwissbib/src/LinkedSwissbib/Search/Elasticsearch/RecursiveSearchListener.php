<?php

namespace LinkedSwissbib\Search\Elasticsearch;

use VuFindSearch\Backend\BackendInterface;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

class RecursiveSearchListener {

    /**
     * Backend.
     *
     * @var BackendInterface
     */
    protected $backend;


    /**
     * Is recursive search active?
     *
     * @var bool
     */
    protected $active = true;


    /**
     * Constructor.
     *
     * @param BackendInterface        $backend          Search backend
     *
     * @return void
     */
    public function __construct(BackendInterface $backend) {
        $this->backend = $backend;
    }


    /**
     * Attach listener to shared event manager.
     *
     * @param SharedEventManagerInterface $manager Shared event manager
     *
     * @return void
     */
    public function attach(SharedEventManagerInterface $manager)
    {
        $manager->attach('VuFind\Search', 'post', [$this, 'onSearchPost']);
    }


    /**
     *
     *
     * @param EventInterface $event Event
     *
     * @return EventInterface
     */
    public function onSearchPost(EventInterface $event)
    {
        // Do nothing if listener is disabled
        if (!$this->active) {
            return $event;
        }

        $backend = $event->getParam('backend');
        if ($backend == $this->backend->getIdentifier()) {
            $result = $event->getTarget();
            foreach ($result->getRecords() as $record) { // $record is instance of ElasticSearchRDF
                $id = $record->getUniqueId();
            }
        }

        return $event;
    }
}