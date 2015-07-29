<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 7/17/15
 * Time: 5:37 PM
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query;

class BooleanQuery extends CompoundQueryClause
{

   protected $registeredBooleanQueryClasses =
        [
            'must' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Must',
            'must_not' => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\MustNot',
            'should'    => 'LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query\Should',
        ];


    /**
     * @return array
     */
    public function build()
    {
        $booleanQuery = null;
        foreach ($this->registeredBooleanQueryClasses as $key => $boolClause)
        {
            if (array_key_exists($key,$this->spec))
            {
                /** @var Query $queryClass */
                $queryClass = new $boolClause($this->query, $this->spec[$key]);
                $this->addClause($queryClass);
            }
        }

        /** @var Query $clause */
        foreach ($this->clauses as $clause)
        {
            $booleanQuery['bool'] = $clause->build();
        }

        return $booleanQuery;

    }
}