<?php
namespace ElasticaTest;

use Elastica;

class Result
{
    /* @var $elasticaResultSet Elastica\ResultSet */
    private $elasticaResultSet;

    function __construct($elasticaResultSet)
    {
        $this->elasticaResultSet = $elasticaResultSet;
    }

    public function getResults()
    {
        $elasticaResults = $this->elasticaResultSet->getResults();
        $results = [];
        foreach ($elasticaResults as $elasticaResult) {
            $results[] = $elasticaResult->getData();
        }

        return $results;
    }

    public function getNumResults()
    {
        return $this->elasticaResultSet->getTotalHits();
    }

    public function getFacets()
    {
        // Get facets from the result of the search query
        return $this->elasticaResultSet->getFacets();
    }
}