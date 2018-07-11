<?php

namespace Tests\AppBundle\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private $client;

    public function testGetCategoriesSuccess()
    {

        $this->client->request(
            'GET',
            '/api/categories',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-USERNAME' => 'olga@mail.com',
                'HTTP_X-PASSWORD' => 'test'
            )
        );

        $expected = '[{"id":3,"name":"Action"},{"id":1,"name":"Comedy"},{"id":4,"name":"Detective"},{"id":2,"name":"Drama"}]';

        $this->assertEquals($expected, $this->client->getResponse()->getContent());

        // echo $this->client->getResponse()->getContent();

    }

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        $this->client = null;
    }
}