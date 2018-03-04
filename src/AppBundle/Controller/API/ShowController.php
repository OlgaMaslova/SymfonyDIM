<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Category;
use AppBundle\Entity\Show;
use AppBundle\File\FileUploader;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;


/**
 * @Route(name="api_show_")
 */
class ShowController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/shows", name="list")
     *
     * @SWG\Get(
     *     path="/api/shows",
     *     summary="Returns the the list of shows",
     *      @SWG\Response(
     *          response=200,
     *          description="Returns the the list of shows",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=Show::class, groups={"show"})
     *          )
     *      )
     * )
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
     *
     * @SWG\Get(
     *     path="/api/shows/{id}",
     *     summary="Returns the show by its id",
     *      @SWG\Response(
     *          response=200,
     *          description="Returns the show by its id",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=Show::class, groups={"show"})
     *          )
     *      )
     * )
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
     *
     * @SWG\Post(
     *     path="/api/shows",
     *     summary="Creates a show",
     *     @SWG\Parameter(
     *       name="JSON create body",
     *       in="body",
     *       description="json object",
     *       type="json",
     *       required=true,
     *       @SWG\Schema(
     *        type="object",
     *           @SWG\Property(
     *                type="string",
     *                property="name",
     *                example="Sherlock",
     *                description="name of the show"
     *           ),
     *           @SWG\Property(
     *                type="string",
     *                property="abstract",
     *                example="Sherlock Holmes in modern London",
     *                description="abstract of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="country",
     *                example="UK",
     *                description="country of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="releaseDate",
     *                example="24 Oct 2013",
     *                description="release Date"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="category",
     *                example={"name": "Crime"},
     *                description="new or existing category described by its name"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="author",
     *                example={"fullname": "Olga"},
     *                description="existing in DB author described by his fullname"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="path",
     *                example="/Users/olga/sherlock.jpg",
     *                description="full local path to the image of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="filename",
     *                example="sherlock.jpg",
     *                description="filename of the image file"
     *          ),
     *        ),
     *      ),
     *      @SWG\Response(
     *       response=201,
     *       description="Show is updated"
     *      ),
     *      @SWG\Response(
     *       response=404,
     *       description="Validation error"
     *      )
     * )
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
     *
     * @SWG\Put(
     *     path="/api/shows/{id}",
     *     summary="Updates a show by its id",
     *     @SWG\Parameter(
     *       name="JSON update body",
     *       in="body",
     *       description="json object",
     *       type="json",
     *       required=true,
     *       @SWG\Schema(
     *        type="object",
     *           @SWG\Property(
     *                type="string",
     *                property="name",
     *                example="Sherlock",
     *                description="name of the show"
     *           ),
     *           @SWG\Property(
     *                type="string",
     *                property="abstract",
     *                example="Sherlock Holmes in modern London",
     *                description="abstract of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="country",
     *                example="UK",
     *                description="country of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="releaseDate",
     *                example="24 Oct 2013",
     *                description="release Date"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="category",
     *                example={"name": "Crime"},
     *                description="new or existing category described by its name"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="author",
     *                example={"fullname": "Olga"},
     *                description="existing in DB author described by his fullname"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="path",
     *                example="/Users/olga/sherlock.jpg",
     *                description="full local path to the image of the show"
     *          ),
     *          @SWG\Property(
     *                type="string",
     *                property="filename",
     *                example="sherlock.jpg",
     *                description="filename of the image file"
     *          ),
     *        ),
     *      ),
     *      @SWG\Response(
     *       response=201,
     *       description="Show is updated"
     *      ),
     *      @SWG\Response(
     *       response=404,
     *       description="Validation error"
     *      )
     * )
     *
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
     *
     * @SWG\Delete(
     *      path="/api/shows/{id}",
     *      summary="Deletes the show by his id",
     *      @SWG\Response(
     *        response=200,
     *        description="Show is deleted"
     *      ),
     *     @SWG\Response(
     *       response=404,
     *       description="Error"
     *     )
     * )
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