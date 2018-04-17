<?php


namespace Tests\AppBundle\ShowFinder;

use AppBundle\Entity\Category;
use AppBundle\Entity\Show;
use AppBundle\Entity\User;
use AppBundle\File\FileUploader;
use AppBundle\ShowFinder\OMDBShowFinder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\File\File;


class OMDBShowFinderTest extends TestCase
{
    public function testOMDBReturnsNoShows()
    {
        $fileUploader = new FileUploader('/upload', '/Users/digital/showroom');

        $apikey = 'be9bb5cf';

        $tokenStorage = $this->createMock(TokenStorage::class);

        $results = $this
            ->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $results->method('getBody')->willReturn('{"Response":"False", "Error":"Series not found!"}');

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->method('__call')
            ->with($this->equalTo('get'))
            ->willReturn($results)
        ;

        $omdbShowFinder = new OMDBShowFinder($client, $apikey, $fileUploader, $tokenStorage);

        $res = $omdbShowFinder->findByName('My research');

        $this->assertSame([], $res);

    }

    public function testFindShow()
    {
        $fileUploader = $this
            ->getMockBuilder(FileUploader::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $fileUploader->method('getUploadDirectoryPath')->willReturn('/Users/digital/showroom/web/upload');

        $apikey = 'be9bb5cf';
        $user =new User();
        $token = $this
            ->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this
            ->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $tokenStorage->method('getToken')->willReturn($token);

        $results = $this
            ->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $results->method('getBody')->willReturn('{"Title" : "Friends", "Released" : "22 Sep 1994", "Genre" : "Comedy, Romance", 
                  "Plot" : "Follows the personal and professional lives of six 20 to 30-something-year-old friends living in Manhattan.",                                                                  
                  "Country" : "USA", "Poster" : "https://images-na.ssl-images-amazon.com/images/M/MV5BMTg4NzEyNzQ5OF5BMl5BanBnXkFtZTYwNTY3NDg4._V1._CR24,0,293,443_SX89_AL_.jpg_V1_SX300.jpg", "Response" : "True"}');

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->method('__call')
            ->with($this->equalTo('get'))
            ->willReturn($results)
        ;

        $omdbShowFinder = new OMDBShowFinder($client, $apikey, $fileUploader, $tokenStorage);

        $res = $omdbShowFinder->findByName('Friends');

        //create an expected show from the search
        $show = new Show();
        $category = new Category();
        $category->setName("Comedy, Romance");
        $show->setName("Friends");
        $show->setDbSource(Show::DATA_SOURCE_OMDB);
        $show->setAbstract("Follows the personal and professional lives of six 20 to 30-something-year-old friends living in Manhattan.");
        $show->setCategory($category);
        $show->setAuthor($user);
        $show->setCountry("USA");
        $show->setReleaseDate(new \DateTime("22 Sep 1994"));
        $image = imagecreatefromjpeg("https://images-na.ssl-images-amazon.com/images/M/MV5BMTg4NzEyNzQ5OF5BMl5BanBnXkFtZTYwNTY3NDg4._V1._CR24,0,293,443_SX89_AL_.jpg_V1_SX300.jpg");
        $path = '/Users/digital/showroom/web/upload/tmp.jpg';
        imagejpeg($image, $path);
        $show->setMainPicture(new File($path));
        $show->setMainPictureFileName('tmp.jpg');
        $expectedShows[]= $show;

        $this->assertEquals($expectedShows, $res);
    }
}