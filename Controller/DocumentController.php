<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Form\DocumentType;
use App\Entity\Client;
use App\Entity\Documents;
use FOS\RestBundle\Controller\Annotations as REST;
use http\Exception\InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Rest\RouteResource("Document", pluralize=false)
 */
class DocumentController extends AbstractController
{

    const NEW_DOCUMENT_EVENT_NAME = 'client.new_document';

    /**
     * @Operation(
     *     tags={"Документы (document)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="deleted",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="counteragentId",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="number",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when created successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when invalid value"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returns if the object reference is not found"
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Returned when unique field conflict"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     * @REST\View()
     * @param Request $request
     *
     * @return object
     */
    public function postAction(Request $request)
    {
        return $this->postForm($request);
    }


    /**
     * @Operation(
     *     tags={"Документы (document)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="deleted",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="counteragentId",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="number",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when updated successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when invalid value"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returns if the object reference is not found"
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Returned when unique field conflict"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     * @REST\Route(requirements={ "id": "\d+" })
     * @REST\View()
     * @param         $id
     * @param Request $request
     *
     * @return object
     */
    public function postPatchAction($id, Request $request)
    {
        $documentsRepository = $this->getRepository(Documents::class);
        $old_documents       = $documentsRepository->find($id);

        if (!$old_documents) {
            throw new NotFoundHttpException();
        }

        return $this->patchForm($old_documents, $request);
    }

    /**
     * @inheritdoc
     */
    public function toLocalDateTime($value)
    {
        if (!preg_match('/\+\d+:\d+$/', $value)) {
            $value .= '+00:00';
        }

        $inputDateTime = new \DateTime($value);
        $timestamp     = $inputDateTime->getTimestamp();

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }


    /**
     * @param Documents $documents
     *
     * @return Documents|array
     */
    private function setFileUrl(Documents $documents)
    {

        $file = $documents->getFile();
        if (!$file) {
            return $file;
        }

        $parameters = [
            'accountId'  => $documents->getClient()->getAccountId(),
            'documentId' => $documents->getId(),
            'name'       => 'file',
            'ext'        => 'pdf'
        ];

        $url = $this->generateUrl('api_download_documents', $parameters, UrlGeneratorInterface::ABSOLUTE_URL);

        $documents->setFileUrl($url);

        return $documents;
    }

    /**
     * @inheritdoc
     */
    private function postForm(Request $request)
    {
        $documents = new Documents();

        $conflicts = [];
        $invalid   = [];

        $form = $this->createForm(DocumentType::class, $documents);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$documents->getCounteragentId()) {
                $invalid[] = [
                    'field'   => 'couteragent_id',
                    'value'   => $documents->getCounteragentId(),
                    'message' => 'Required: couteragent_id'
                ];
            }

            if (!$documents->getNumber()) {
                $invalid[] = [
                    'field'   => 'number',
                    'value'   => $documents->getNumber(),
                    'message' => 'Required: number'
                ];
            }

            if (!$documents->getName()) {
                $invalid[] = [
                    'field'   => 'name',
                    'value'   => $documents->getName(),
                    'message' => 'Required: name'
                ];
            }

            if (!$documents->getType()) {
                $invalid[] = [
                    'field'   => 'type',
                    'value'   => $documents->getType(),
                    'message' => 'Required: type BILL | ACT | RECONCILIATION_ACT'
                ];
            }

            if ($invalid) {
                return $this->errorView($conflicts, $invalid);
            }

            if ($documents->getDate()) {
                $documents->setFormationAt($this->toLocalDateTime($documents->getDate()));
            }

            $documents->setClient($this->getClient($documents->getCounteragentId()));

            $violations = $this->validator->validate($documents);

            if ($violations->count() != 0) {
                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $constraint = $violation->getConstraint();
                    $key        = $violation->getPropertyPath();
                    $value      = $violation->getInvalidValue();

                    if ($constraint instanceof UniqueEntity) {
                        $param       = [$key => $value];
                        $record      = $this->findOneBy($param);
                        $conflicts[] = [
                            'field'         => $key,
                            'value'         => $value,
                            'message'       => $violation->getMessage(),
                            'conflict_with' => $record
                        ];
                    } else {
                        $invalid[] = [
                            'field'   => $key,
                            'value'   => $value,
                            'message' => $violation->getMessage()
                        ];
                    }
                }

                return $this->errorView($conflicts, $invalid);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($documents);
            $em->flush();

            $this->setFileUrl($documents);

            $this->createEvent($documents);

            $view = $this->view($documents, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        return $form;
    }

    /**
     * @inheritdoc
     */
    private function patchForm(Documents $oldDocument, Request $request)
    {
        $documents = new Documents();

        $conflicts = [];
        $invalid   = [];

        $form = $this->createForm(DocumentType::class, $documents);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($documents->getUuid1c() !== null) {
                $oldDocument->setUuid1c($documents->getUuid1c());
            }

            if ($documents->getDeleted() !== null) {
                $oldDocument->setDeleted($documents->getDeleted());
            }

            if ($documents->getCounteragentId() !== null) {
                $oldDocument->setClient($this->getClient($documents->getCounteragentId()));
            }

            if ($documents->getNumber() !== null) {
                $oldDocument->setNumber($documents->getNumber());
            }

            if ($documents->getDate() !== null) {
                $oldDocument->setFormationAt($this->toLocalDateTime($documents->getDate()));
            }

            if ($documents->getName() !== null) {
                $oldDocument->setName($documents->getName());
            }

            if ($documents->getType() !== null) {
                $oldDocument->setType($documents->getType());
            }

            if ($documents->getFile() !== null) {
                $oldDocument->setFile($documents->getFile());
                $oldDocument->setFileUpload($documents->getFileUpload());
                $oldDocument->setFileInfo($documents->getFileInfo());
            }

            $violations = $this->validator->validate($oldDocument);

            if ($violations->count() != 0) {
                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $constraint = $violation->getConstraint();
                    $key        = $violation->getPropertyPath();
                    $value      = $violation->getInvalidValue();

                    if ($constraint instanceof UniqueEntity) {
                        $param       = [$key => $value];
                        $record      = $this->findOneBy($param);
                        $conflicts[] = [
                            'field'         => $key,
                            'value'         => $value,
                            'message'       => $violation->getMessage(),
                            'conflict_with' => $record
                        ];
                    } else {
                        $invalid[] = [
                            'field'   => $key,
                            'value'   => $value,
                            'message' => $violation->getMessage()
                        ];
                    }
                }

                return $this->errorView($conflicts, $invalid);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($oldDocument);
            $em->flush();

            $this->setFileUrl($oldDocument);

            $this->createEvent($oldDocument);

            $view = $this->view($oldDocument, Response::HTTP_OK);

            return $this->handleView($view);
        }

        return $form;
    }

    /**
     * @param $id
     *
     * @return Client
     */
    private function getClient($id)
    {
        $client = $this->getRepository(Client::class)->findOneBy(['accountId' => (integer)$id]);
        if (!$client) {
            throw $this->createNotFoundException('client not found');
        }

        return $client;
    }

    /**
     * @param $param
     *
     * @return Documents
     */
    private function findOneBy($param)
    {
        return $this->getRepository(Documents::class)->findOneBy($param);
    }

    /**
     * @param Documents $documents
     */
    private function createEvent(Documents $documents)
    {
        $eventName = $this::NEW_DOCUMENT_EVENT_NAME;
        $event     = new GenericEvent($documents);

        $this->get('event_dispatcher')->dispatch($eventName, $event);

    }

}