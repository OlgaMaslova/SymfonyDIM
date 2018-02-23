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

        //Upload the picture
        $image =  new UploadedFile( $json['path'], $json['filename'], 'image/jpeg',null,null,true);

        $fileName = $fileUploader->upload($image, $show->getCategory()->getName());

        $show->setMainPictureFileName($fileName);
        //For validation
        $show->setMainPicture(new File($fileUploader->getUploadDirectoryPath().'/'.$fileName));

        $constraintValidationList = $validator->validate($show, null, ["create"]);

        if($constraintValidationList->count() == 0) {
            //Get the author if he exists

            $author = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('fullname' => $show->getAuthor()->getFullname()));

            $category = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Category')->findBy(array('name' => $show->getCategory()->getName()));

            //Create new Category to persist if needed
            if ($category === []) {
                $newCategory = new Category();
                $newCategory->setName($show->getCategory()->getName());
                $show->setCategory($newCategory);
            } else {
                $show->setCategory($category[0]);
            }

            if ($author != []) {
                $show->setDbSource(Show::DATA_SOURCE_DB);
                $show->setAuthor($author[0]);
                $show->setMainPicture($show->getMainPictureFileName());
                $em = $this->getDoctrine()->getManager();
                $em->persist($show);
                $em->flush();
            }

            return $this->returnResponse('Show is created', Response::HTTP_CREATED);
        }

        return $this->returnResponse($serializer->serialize($constraintValidationList, 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Method({"PUT"})
     * @Route("/shows/{id}", name="update", requirements={"id"="\d+"})
     */
    public function updateAction(Show $show, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $serializationContext = DeserializationContext::create();

        $newShow = $serializer->deserialize($request->getContent(), Show::class, 'json', $serializationContext->setGroups(["show_update"]));

        $constraintValidationList = $validator->validate($newShow);

        if($constraintValidationList->count() == 0) {

            //Check if the category exists, then update
            $categories = $this->getDoctrine()->getManager()->getRepository('AppBundle\Entity\Category')->findAll();
            dump($newShow);die;
            if (in_array($newShow->getCategory(), $categories)) {
                $show->update($newShow);

                $this->getDoctrine()->getManager()->flush();

                return $this->returnResponse('Show is updated', Response::HTTP_OK);
            } else {
                return $this->returnResponse('Category does not exist', Response::HTTP_BAD_REQUEST);
            }
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

}