<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Media;
use AppBundle\File\FileUploader;
use AppBundle\Type\MediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/media", name="media_")
 */
class MediaController extends Controller
{
    /**
     * @Method({"POST"})
     * @Route("/")
     */
    public function uploadAction(Request $request, FileUploader $fileUploader, RouterInterface $router)
    {
        $media = new Media();
        //$form = $this->createForm(MediaType::class, $media);

        $media->setFile($request->files->get('file'));

        // Validate media object

        //if($form->isValid()) {}

        $generatedFileName = $fileUploader->upload($media->getFile(), time());

        $path = $this->container->getParameter('upload_directory_file').'/'.$generatedFileName;

        $baseUrl = $router->getContext()->getScheme().'://'.$router->getContext()->getHost().':'.$router->getContext()->getHttpPort();

       // dump($baseUrl.$path);die;
        $media->setPath($baseUrl.$path);

        $em = $this->getDoctrine()->getManager();
        $em->persist($media);
        $em->flush();

        return $this->returnResponse('', Response::HTTP_CREATED);

       // return $this->returnResponse('', Response::HTTP_BAD_REQUEST);
    }

}