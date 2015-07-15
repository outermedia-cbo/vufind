<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/14/15
 * Time: 12:42 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch;


class SearchHandler
{


    /**
     * Known configuration keys.
     *
     * @var array
     */
    //protected static $configKeys = [
    //    'CustomMunge', 'DismaxFields', 'DismaxHandler', 'QueryFields',
    //    'DismaxParams', 'FilterQuery'
    //];

    protected static $configKeys = [
        'DismaxFields', 'DismaxHandler', 'QueryFields',
        'DismaxParams', 'FilterQuery', 'UsedTypes', 'UsedIndex'
    ];



    /**
     * Search handler specification.
     *
     * @var array
     */
    protected $specs = [];

    /**
     * Constructor.
     *
     * @param array  $specs                Search handler specifications
     * @param string $defaultDismaxHandler Default dismax handler (if no
     * DismaxHandler set in specs).
     *
     * @return void
     */
    public function __construct(array $spec = [],
                                $defaultDismaxHandler = 'dismax') {
        foreach (self::$configKeys as $key) {
            $this->specs[$key] = isset($spec[$key]) ? $spec[$key] : [];
        }
        // Set dismax handler to default if not specified:
        if (empty($this->specs['DismaxHandler'])) {
            $this->specs['DismaxHandler'] = $defaultDismaxHandler;
        }
    }



    /**
     * Return defined dismax fields.
     *
     * @return array
     */
    public function getDismaxFields()
    {
        return $this->specs['DismaxFields'];
    }

    /**
     * Return defined dismax fields.
     *
     * @return array
     */
    public function getIndices()
    {
        //return [];
        return $this->specs['UsedIndex'];
    }


    /**
     * Return the types (as part of the index) defined for this specific search handler .
     *
     * @return array
     */
    public function getTypes()
    {
        //todo: implement this -> we need this to build the query!
        return null;
    }


}