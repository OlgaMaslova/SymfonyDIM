<?php
namespace AppBundle\ShowFinder;

use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\DateTime;

class OMDBShowFinder implements ShowFinderInterface

{
    private $client;
    private $apikey;
    private $fileUploader;
    public function __construct(Client $client, $apikey, FileUploader $fileUploader)
    {
        $this->client = $client;
        $this->apikey = $apikey;
        $this->fileUploader = $fileUploader;
    }

    public function findByName($query)
    {
        $results =  $this->client->get($this->apikey.'&type=series&t="'.$query.'"');
        //dump(\GuzzleHttp\json_decode($results->getBody(), true));die;
        $array = json_decode($results->getBody(), true);
        $show = new Show();
        $show->setName($array["Title"]);
        $show->setAbstract($array["Plot"]);
        $show->setCategory($array["Genre"]);
        $show->setAuthor($array["Writer"]);
        $show->setCountry($array["Country"]);
        $date = \DateTime::createFromFormat('d M Y', $array["Released"]);
        $show->setReleaseDate($date);
        $image = imagecreatefromjpeg($array["Poster"]);
        $path = $this->fileUploader->getUploadDirectoryPath().'/tmp.jpg';
        imagejpeg($image, $path);
        $show->setMainPicture(new File($path));
        $show->setMainPictureFileName('tmp.jpg');
        $show->setDbSource("(IMDB)");
        return $show;
    }

    public function getName() {
        return 'IMDB API';
    }

}