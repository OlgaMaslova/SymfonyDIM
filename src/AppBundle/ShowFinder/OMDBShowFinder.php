<?php
namespace AppBundle\ShowFinder;

use GuzzleHttp\Client;

class OMDBShowFinder implements ShowFinderInterface

{
    private $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function findByName($query) {
       $results =  $this->client->get('/?apikey=be9bb5cf&type=series&t="friends"');
       dump(\GuzzleHttp\json_decode($results->getBody(), true));die;
    }

    public function getName() {
        return 'IMDB API';
    }

}