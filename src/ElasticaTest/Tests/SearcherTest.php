<?php
namespace ElasticaTest\Tests;

use ElasticaTest\Indexer;
use ElasticaTest\Searcher;
use Elastica;

class SearcherTest extends \PHPUnit_Framework_TestCase
{
    const INDEX_NAME = 'twitter';
    const TYPE_NAME = 'tweet';

    /* @var $indexer Indexer */
    private $indexer;

    public function setUp()
    {
        $this->indexer = new Indexer(new \Elastica\Client(), self::INDEX_NAME, self::TYPE_NAME);
        $this->indexer->deleteIndex();
        $this->indexer->build();

        // Add documents to the index
        $this->indexer->addTweets($this->getTwitts());
    }

    /**
     * @test
     */
    public function it_should_search_by_term()
    {
        $searcher = new Searcher($this->indexer);
        $result = $searcher->searchByTerm('Oriol');

        $results  = $result->getResults();
        $totalResults = $result->getNumResults();

        $this->assertEquals(1, $totalResults);
        $this->assertEquals('Oriol', $results[0]['user']['name']);
    }

    /**
     * @test
     */
    public function it_should_search_by_skill()
    {
        $searcher = new Searcher($this->indexer);
        $result = $searcher->searchBySkills(['php']);

        $results  = $result->getResults();
        $totalResults = $result->getNumResults();

        $this->assertEquals(1, $totalResults);
        $this->assertEquals('Oriol', $results[0]['user']['name']);
    }

    public function getTwitts()
    {
        return [
            1 =>
            [
                'id'      => 1,
                'user'    => array(
                    'name'      => 'mewantcookie',
                    'fullName'  => 'Cookie Monster',
                    'skill'     => 'none'
                ),
                'msg'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
                'tstamp'  => '1238081389',
                'location'=> '41.12,-71.34',
                '_boost'  => 1.0
            ],
            2 =>
            [
                'id'      => 2,
                'user'    => array(
                    'name'      => 'Oriol',
                    'fullName'  => 'Oriol Gonzalez',
                    'skill'     => 'php'
                ),
                'skills'  => 'php',
                'msg'     => 'Hello I am web developer',
                'tstamp'  => '1238081389',
                'location'=> '41.12,-71.34',
                '_boost'  => 1.0
            ],
            3 =>
            [
                'id'      => 3,
                'user'    => array(
                    'name'      => 'Joaquin',
                    'fullName'  => 'Joaquin Tarrago',
                    'skill'     => 'java'
                ),
                'skills'  => 'java',
                'msg'     => 'Hello Im also a web developer',
                'tstamp'  => '1238081389',
                'location'=> '41.12,-71.34',
                '_boost'  => 1.0
            ],
            4 =>
                [
                    'id'      => 4,
                    'user'    => array(
                        'name'      => 'Zack',
                        'fullName'  => 'Zack Marquez',
                        'skill'     => 'solr'
                    ),
                    'skills'  => 'solr',
                    'msg'     => 'Hello Im also another web developer',
                    'tstamp'  => '1238081389',
                    'location'=> '41.12,-71.34',
                    '_boost'  => 1.0
                ],
            5 =>
                [
                    'id'      => 5,
                    'user'    => array(
                        'name'      => 'Raul',
                        'fullName'  => 'Raul Fernandez',
                        'skill'     => 'solr'
                    ),
                    'skills'   => 'solr',
                    'msg'     => 'Hello Im also another web developer',
                    'tstamp'  => '1238081389',
                    'location'=> '41.12,-71.34',
                    '_boost'  => 1.0
                ],
            6 =>
                [
                    'id'      => 6,
                    'user'    => array(
                        'name'      => 'Eber',
                        'fullName'  => 'Eber Herrera',
                        'skill'     => 'javacript'
                    ),
                    'skills'   => 'solr',
                    'msg'     => 'Hello Im also another web developer',
                    'tstamp'  => '1238081389',
                    'location'=> '41.12,-71.34',
                    '_boost'  => 1.0
                ]
        ];
    }
}