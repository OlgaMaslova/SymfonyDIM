<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    public function testLogin()
    {
       $crawler = $this->client->request('GET', '/login');
       $this->assertContains('Welcome!', $crawler->filter('h1')->text());

       $form = $crawler->selectButton('Connect')->form();
       $crawler = $this->client->submit(
           $form,
           [
               'email' => 'olga@mail.com',
               'password' => 'test'
           ]
       );

        $crawler = $this->client->followRedirect();

        $this->assertContains('List of shows', $crawler->filter('h1')->text());

       echo $this->client->getResponse()->getContent();

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