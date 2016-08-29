<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch_Response
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace LinkedSwissbib\Backend\Elasticsearch\Response;

use VuFindSearch\Response\RecordCollectionFactoryInterface;
use VuFindSearch\Response\RecordCollectionInterface;
use VuFindSearch\Exception\InvalidArgumentException;


class RecordCollectionFactory implements RecordCollectionFactoryInterface {


    /**
     * Factory to turn data into a record object.
     *
     * @var Callable
     */
    protected $recordFactory;

    /**
     * Class of collection.
     *
     * @var string
     */
    protected $collectionClass;



    /**
     * Constructor.
     *
     * @param Callable $recordFactory   Callback to construct records
     * @param string   $collectionClass Class of collection
     *
     * @return void
     */
    public function __construct($recordFactory = null,
                                $collectionClass = 'LinkedSwissbib\Backend\Elasticsearch\Response\RecordCollection'
    ) {
        if (null === $recordFactory) {
            $this->recordFactory = function ($data) {
                return new Record($data);
            };
        } else {
            $this->recordFactory = $recordFactory;
        }
        $this->collectionClass = $collectionClass;
    }



    /**
     * Convert a response into a record collection.
     *
     * @param mixed $response Raw response data
     *
     * @return RecordCollectionInterface
     */
    public function factory($responses)
    {
        if (!is_array($responses)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unexpected type of value: Expected array, got %s',
                    gettype($responses)
                )
            );
        }

        $collection = new $this->collectionClass($responses);
        $totalHits = 0;
        foreach ($responses['responses'] as $response) {
            if (isset($response['hits']['total'])) {
                $totalHits += $response['hits']['total'];
            }

            if (isset($response['hits']['hits'])) {
                foreach ($response['hits']['hits'] as $hit) {
                    $collection->add(call_user_func($this->recordFactory, $hit));
                }
            }
        }

        $collection->setTotal($totalHits);

        return $collection;
    }
}