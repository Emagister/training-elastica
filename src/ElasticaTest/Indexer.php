<?php
namespace ElasticaTest;

use Elastica;
use Elastica\Type\Mapping;

class Indexer
{
    private $client;
    /* @var $index Elastica\Index' */
    private $index;
    private $indexName;
    private $typeName;
    private $type;

    /**
     * @param $client
     * @param $indexName
     * @param $typeName
     */
    function __construct($client, $indexName, $typeName)
    {
        $this->client = $client;
        $this->indexName = $indexName;
        $this->typeName = $typeName;

        $this->index = $this->loadIndex($this->indexName);
    }

    public function build()
    {
        $this->index = $this->loadIndex($this->indexName);
        $this->createIndex($this->getIndexConfiguration());
        $this->createType($this->typeName);
        $this->createMapping();
    }

    private function createIndex($config)
    {
        $this->index->create(
            $config,
            true
        );
    }

    public function getIndex()
    {
        return $this->index;
    }

    private function createType($name)
    {
        $this->type = $this->getIndex()->getType($name);
    }

    /**
     * @return \Elastica\Type
     */
    public function getType()
    {
        return $this->type;
    }

    private function getIndexConfiguration()
    {
        return array(
            'number_of_shards' => 4,
            'number_of_replicas' => 1,
            'analysis' => array(
                'analyzer' => array(
                    'indexAnalyzer' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array('lowercase')
                    ),
                    'searchAnalyzer' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array('standard', 'lowercase')
                    )
                )
            )
        );
    }

    private function createMapping()
    {
        // Define mapping
        $mapping = new Mapping();
        $mapping->setType($this->getType());
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'user'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'fullName'  => array('type' => 'string', 'include_in_all' => TRUE),
                    'skill'     => array('type' => 'string', 'include_in_all' => TRUE),
                ),
            ),
            'skills'  => array('type' => 'string', 'include_in_all' => TRUE),
            'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));

        // Send mapping to type
        $mapping->send();
    }

    public function addtweet($id, $tweet)
    {
        $elasticaType = $this->getType();

        // First parameter is the id of document.
        $document = new \Elastica\Document($id, $tweet);

        // Add tweet to type
        $elasticaType->addDocument($document);

        // Refresh Index
        $elasticaType->getIndex()->refresh();
    }

    public function addtweets($tweets)
    {
        $elasticaType = $this->getType();

        $documents = [];
        foreach ($tweets as $id => $tweet) {
            $documents[] = new \Elastica\Document($id, $tweet);
        }

        // Add tweet to type
        $elasticaType->addDocuments($documents);

        // Refresh Index
        $elasticaType->getIndex()->refresh();
    }

    public function deleteIndex()
    {
        if ($this->getIndex()->exists()) {
            $this->getIndex()->delete();
        }
    }

    public function exists()
    {
        return $this->getIndex()->exists();
    }

    /**
     * @param $indexName
     */
    private function loadIndex($indexName)
    {
        return $this->client->getIndex($indexName);
    }
}