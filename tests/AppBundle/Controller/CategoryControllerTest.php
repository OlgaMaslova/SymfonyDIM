<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private $client;

    public function testCreateCategorySuccess()
    {
        $this->login();

        $crawler = $this->client->request('GET', '/category/create');
        $this->assertContains('Create a new category', $crawler->filter('h1')->text());

        $name = time(); //to create a new category every time the code is executed

        $form = $crawler->selectButton('Save')->form();
        $crawler = $this->client->submit(
            $form,
            ['category[name]' => $name]
        );

        $crawler = $this->client->followRedirect();

        $this->assertContains('You successfully added a new category!', $crawler->filter('html')->text());

        $link = $crawler->selectLink('Create category')->link();  //to fetch the link
        $crawler = $this->client->click($link);  //clicking the link

        $this->assertContains('Create a new category', $crawler->filter('h1')->text());

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

    /**
     * Logs in the user
     * @return Symfony\Component\DomCrawler\Crawler $crawler
     *
     */
    private function login()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Connect')->form();
        $crawler = $this->client->submit(
            $form,
            [
                'email' => 'olga@mail.com',
                'password' => 'test'
            ]
        );

        return $this->client->followRedirect();
    }
}