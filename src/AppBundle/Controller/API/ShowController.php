<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Category;
use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use finfo;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route(name="api_show_")
 */
class ShowController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/shows", name="list")
     */
    public function listAction(SerializerInterface $serializer)
    {
        $shows = $this->getDoctrine()->getRepository('AppBundle:Show')->findAll();

        $serializationContext = SerializationContext::create();

        return $this->returnResponse($serializer->serialize($shows, 'json', $serializationContext->setGroups(["show"])),
            Response::HTTP_OK);
    }

    /**
     * @Method({"GET"})
     * @Route("/shows/{id}", name="get", requirements={"id"="\d+"})
     */
    public function getShowAction(Show $show, SerializerInterface $serializer)
    {
        $serializationContext = SerializationContext::create();

        return $this->returnResponse($serializer->serialize($show, 'json', $serializationContext->setGroups(["show"])),
            Response::HTTP_OK);
    }

    /**
     * @Method({"POST"})
     * @Route("/shows", name="create")
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, FileUploader $fileUploader)
    {
        $serializationContext = DeserializationContext::create();

        $show = $serializer->deserialize($request->getContent(), Show::class, 'json', $serializationContext->setGroups(["show_create"]));

        $show->setReleaseDate(new \DateTime($show->getReleaseDate()));

        $json = json_decode($request->getContent(), true);

        $show->setMainPictureFromPath($json, $fileUploader);

        $constraintValidationList = $validator->validate($show, null, ["create"]);

        if($constraintValidationList->count() == 0) {
            $this->setCategory($show);

            //Get the author if he exists
            $author = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('fullname' => $show->getAuthor()->getFullname()));

            if ($author != []) {
                $show->setDbSource(Show::DATA_SOURCE_DB);
                $show->setAuthor($author[0]);
                $show->setMainPicture($show->getMainPictureFileName());
                $em = $this->getDoctrine()->getManager();
                $em->persist($show);
                $em->flush();
                return $this->returnResponse('Show is created', Response::HTTP_CREATED);
            }
            return $this->returnResponse('Author not found', Response::HTTP_BAD_REQUEST);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Method({"PUT"})
     * @Route("/shows/{id}", name="update", requirements={"id"="\d+"})
     */
    public function updateAction(Show $show, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, FileUploader $fileUploader)
    {
        $serializationContext = DeserializationContext::create();

        $newShow = $serializer->deserialize($request->getContent(), Show::class, 'json', $serializationContext->setGroups(["show_update"]));

        $newShow->setReleaseDate(new \DateTime($newShow->getReleaseDate()));

        $json = json_decode($request->getContent(), true);

        if(isset($json['path']) && isset($json['filename'])) {
            //the path is provided->set new picture
            $newShow->setMainPictureFromPath($json, $fileUploader);
        } else {
            //Set the existing picture in the show
            $newShow->setMainPicture($show->getMainPicture());
            $newShow->setMainPictureFilename($show->getMainPictureFileName());
        }

        $constraintValidationList = $validator->validate($newShow, null, ["update"]);

        if($constraintValidationList->count() == 0) {

            $this->setCategory($newShow);

            //Get the author and update if he exists
            $author = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('fullname' => $newShow->getAuthor()->getFullname()));

            if ($author != []) {
                $show->setAuthor($author[0]);
                $show->update($newShow);
                $this->getDoctrine()->getManager()->flush();
                return $this->returnResponse('Show is updated', Response::HTTP_OK);
            }
            return $this->returnResponse('Author not found', Response::HTTP_BAD_REQUEST);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Method({"DELETE"})
     * @Route("/shows/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAction(Show $show)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($show);
        $em->flush();

        return $this->returnResponse('Show is deleted', Response::HTTP_OK);
    }

    /**
     * Create or validate existing category for a show
     * @param $show
     */
    public function setCategory($show)
    {
        $category = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Category')->findBy(array('name' => $show->getCategory()->getName()));

        //Create new Category to persist if needed
        if ($category === []) {
            $newCategory = new Category();
            $newCategory->setName($show->getCategory()->getName());
            $show->setCategory($newCategory);
        } else {
            $show->setCategory($category[0]);
        }
    }
}