<?php
namespace AppBundle\ShowFinder;

use AppBundle\Entity\Category;
use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class OMDBShowFinder implements ShowFinderInterface

{
    private $client;
    private $apikey;
    private $fileUploader;
    private $tokenStorage;

    public function __construct(Client $client, $apikey, FileUploader $fileUploader, TokenStorage $tokenStorage)
    {
        $this->client = $client;
        $this->apikey = $apikey;
        $this->fileUploader = $fileUploader;
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * Find a show by a string
     *
     * @param String $query
     * @return Array $shows
     *
     */
    public function findByName($query)
    {
        $results =  $this->client->get('/?apikey='.$this->apikey.'&type=series&t="'.$query.'"');
        //dump(\GuzzleHttp\json_decode($results->getBody(), true));die;
        $json = \GuzzleHttp\json_decode($results->getBody(), true);
        if ($json['Response'] == 'False' && $json['Error'] == 'Series not found!') {
            return [];
        }

        return $this->convertToShow(json_decode($results->getBody(), true));
    }

    /**
     * Create a private function that tranforms an OMDB JSON into a Show and Category
     * @param String $json
     * @return Shows[] $shows
     */
    private function convertToShow($json)
    {
        $shows = [];
        $show = new Show();
        $category = new Category();
        $category->setName($json["Genre"]);

        $show->setName($json["Title"]);
        $show->setDbSource(Show::DATA_SOURCE_OMDB);
        $show->setAbstract($json["Plot"]);
        $show->setCategory($category);
        $show->setAuthor($this->tokenStorage->getToken()->getUser());
        $show->setCountry($json["Country"]);
        $show->setReleaseDate(new \DateTime($json["Released"]));

        //save locally the poster in temporary file
        if ($json["Poster"] != "N/A") {
            $image = imagecreatefromjpeg($json["Poster"]);
            $path = $this->fileUploader->getUploadDirectoryPath().'/tmp.jpg';
            imagejpeg($image, $path);
            $show->setMainPicture(new File($path));
            $show->setMainPictureFileName('tmp.jpg');
        }
        //$show->setMainPicture($json["Poster"]);

        $shows[]= $show;

        return $shows;

    }

    public function getName() {
        return 'IMDB API';
    }

}