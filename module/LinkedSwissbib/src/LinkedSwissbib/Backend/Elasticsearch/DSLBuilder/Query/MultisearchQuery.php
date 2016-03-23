<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Backend\Elasticsearch\DSLBuilder\Query;


use LinkedSwissbib\Backend\Elasticsearch\ESQueryBuilder;
use Elasticsearch\Serializers\SmartSerializer;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Query\Query as VuFindQuery;

class MultisearchQuery extends Query
{

    protected $queryBuilder;

    public function __construct(VuFindQuery $query, array $querySpec, ESQueryBuilder $queryBuilder)
    {
        $this->query = $query;
        $this->spec = $querySpec;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return array
     */
    public function build()
    {
        $relatedclauses = array();
        $clause = array();

        foreach($this->spec['Multisearch'] as $key){
            $relatedQuery = new VuFindQuery($this->query->getString(),$key,null);
            array_push($relatedclauses, $this->queryBuilder->build($relatedQuery));
        }
        $queryType = $this->spec['query'];
        foreach (array_keys($queryType) as $key)
        {
            if (array_key_exists($key,$this->registeredQueryClasses))
            {
                /** @var Query $queryClass */
                $queryClass = new $this->registeredQueryClasses[$key]($this->query, $queryType[$key]);
                $queryClass->setSearchSpec($queryType[$key]);

                $clause = $queryClass->build();
            }
        }

        $result = $this->buildHead() . $this->buildBody($clause) . "\n";
        foreach($relatedclauses as $related)
            $result .= $related['body'];
        return $result;
    }

    private function buildHead(){
        $handler = $this->queryBuilder->getSearchHandler($this->query->getHandler());
        $result = '{"index":"' . implode(",",$handler->getIndices()) . '","type":"' . implode(",",$handler->getTypes()) .'"}';
        return $result;
    }

    private function buildBody($array){
        return "\n" . '{"query":' . json_encode($array) . "}";
    }
}