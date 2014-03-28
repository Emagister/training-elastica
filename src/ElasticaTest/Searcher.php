<?php
namespace ElasticaTest;

class Searcher
{
    /* @var $indexer Indexer */
    private $indexer;

    function __construct($indexer)
    {
        $this->indexer = $indexer;
    }

    public function searchByTerm($term, $from = 0, $size = 20)
    {
        // Define a Query. We want a string query.
        $elasticaQueryString  = new \Elastica\Query\QueryString();
        $elasticaQueryString->setQuery($term);

        // Create the actual search object with some data.
        $elasticaQuery        = new \Elastica\Query();
        $elasticaQuery->setQuery($elasticaQueryString);

        $elasticaQuery->setFrom($from);
        $elasticaQuery->setSize($size);

        // Search on the index.
        $elasticaSearchResult = $this->indexer->getIndex()->search($elasticaQuery);

        return new Result($elasticaSearchResult);
    }

    public function searchBySkills($skills, $from = 0, $size = 20)
    {
        // Create the actual search object with some data.
        $elasticaQuery        = new \Elastica\Query();

        // Filter for being of color blue
        $elasticaFilterColorBlue  = new \Elastica\Filter\Term();
        //search 'color' = 'blue'
        $elasticaFilterColorBlue->setTerm('skills', 'php');

        // Filter for being of color green
        $elasticaFilterColorGreen = new \Elastica\Filter\Term();
        $elasticaFilterColorGreen->setTerm('skills', 'php');

        // Filter 'or' for the color, adding the color filters
        $elasticaFilterOr     = new \Elastica\Filter\BoolOr();
        $elasticaFilterOr->addFilter($elasticaFilterColorBlue);
        $elasticaFilterOr->addFilter($elasticaFilterColorGreen);

        // Add filter to the search object.
        $elasticaQuery->setFilter($elasticaFilterOr);

        // Define a new facet.
        $elasticaFacet    = new \Elastica\Facet\Terms('matches');
        $elasticaFacet->setField('name');
        $elasticaFacet->setSize(10);
        $elasticaFacet->setOrder('reverse_count');

        // Add that facet to the search query object.
        $elasticaQuery->addFacet($elasticaFacet);

        $elasticaQuery->setFrom($from);
        $elasticaQuery->setSize($size);

        // Search on the index.
        $elasticaSearchResult = $this->indexer->getIndex()->search($elasticaQuery);

        return new Result($elasticaSearchResult);
    }
}