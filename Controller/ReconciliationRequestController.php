<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Model\InputReconciliationRequest;
use App\Entity\Client;
use App\Entity\ReconciliationRequest;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Rest\RouteResource(resource="ReconciliationRequest", pluralize=false)
 */
class ReconciliationRequestController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Запрос на смену акт сверки (reconciliation_request)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="_limit",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_offset",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_order",
     *         in="query",
     *         description="Default: _order[id]=DESC",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="couteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="date_from",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="date_to",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="completed",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Parameter(
     *         name="created",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful|not found"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     * @Rest\Route(path="reconciliation_request")
     * @Rest\QueryParam(name="_limit",  requirements="\d+", default=2, nullable=true, strict=true)
     * @Rest\QueryParam(name="_offset", requirements="\d+", default=0, nullable=true, strict=true)
     * @Rest\QueryParam(name="_order", nullable=true, description="Default: _order[id]=DESC")
     * @Rest\View()
     *
     * @param Request               $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cgetAction(Request $request, ParamFetcherInterface $paramFetcher)
    {

        $_limit  = $paramFetcher->get('_limit');
        $_offset = $paramFetcher->get('_offset');
        $_order  = $paramFetcher->get('_order');

        $order = $this->orderMap($_order);

        $repository = $this->getRepository(ReconciliationRequest::class);

        /** @var InputReconciliationRequest $input */
        $input = $this->requestMap(InputReconciliationRequest::class, $request->query->all());

        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Запрос на смену акт сверки (reconciliation_request)"},
     *     summary="",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when is reconciliation_request not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     *
     * @Rest\Route(path="reconciliation_request/{id}", requirements={ "id": "\d+" })
     * @param $id
     *
     * @return Response|ReconciliationRequest
     */
    public function getAction($id)
    {
        $result = $this->findOneBy(['id' => (integer)$id]);
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * @Operation(
     *     tags={"Запрос на смену акт сверки (reconciliation_request)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="couteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="date_from",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="date_to",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="completed",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Parameter(
     *         name="created",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
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
     * @Rest\Route(path="reconciliation_request/{id}", requirements={ "id": "\d+" })
     * @REST\View()
     * @param         $id
     * @param Request $request
     *
     * @return object
     */
    public function patchAction($id, Request $request)
    {
        $conflicts = [];
        $invalid   = [];

        $reconciliationRequest = $this->findOneBy(['id' => (integer)$id]);

        if (!$reconciliationRequest) {
            throw new NotFoundHttpException();
        }

        $requestFields = $request->request->all();

        /** @var InputReconciliationRequest $input */
        $input = $this->requestMap(InputReconciliationRequest::class, $requestFields);

        $paymentUpdated = $this->patchClass($reconciliationRequest, $input, $requestFields);

        $violations = $this->get('validator')->validate($paymentUpdated);

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
        $em->persist($paymentUpdated);
        $em->flush();

        return $paymentUpdated;
    }


    /**
     * @param ReconciliationRequest      $ReconciliationRequest
     * @param InputReconciliationRequest $input
     *
     * @param array                      $requestFields
     *
     * @return ReconciliationRequest
     */
    private function patchClass(
        ReconciliationRequest $ReconciliationRequest, InputReconciliationRequest $input, array $requestFields
    ) {
        if (array_key_exists('couteragent_id', $requestFields)) {
            $ReconciliationRequest->setClient($this->getClient($input->getCouteragentId()));
        }

        if (array_key_exists('date_from', $requestFields)) {
            $ReconciliationRequest->setDateFrom($this->toLocalDateTime($input->getDateFrom()));
        }

        if (array_key_exists('date_to', $requestFields)) {
            $ReconciliationRequest->setDateTo($this->toLocalDateTime($input->getDateTo()));
        }

        if (array_key_exists('completed', $requestFields)) {
            $ReconciliationRequest->setCompleted($input->getCompleted());
        }

        if (array_key_exists('created', $requestFields)) {
            $ReconciliationRequest->setCreatedAt($this->toLocalDateTime($input->getCreated()));
        }

        return $ReconciliationRequest;
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
     * @return ReconciliationRequest
     */
    private function findOneBy($param)
    {
        return $this->getRepository(ReconciliationRequest::class)->findOneBy($param);
    }

    /**
     * @param InputReconciliationRequest $input
     *
     * @return array
     */
    private function searchMap(InputReconciliationRequest $input)
    {
        $fields = [];

        if ($input->getCouteragentId() !== null) {
            $fields['client']['accountId'] = $input->getCouteragentId();
        }

        if ($input->getDateFrom() !== null) {
            $fields['dateFrom'] = $input->getDateFrom();
        }

        if ($input->getDateTo() !== null) {
            $fields['dateTo'] = $input->getDateTo();
        }

        if ($input->getCompleted() !== null) {
            $fields['completed'] = $input->getCompleted();
        }

        if ($input->getCreated() !== null) {
            $fields['createdAt'] = $input->getCreated();
        }

        return $fields;
    }


    /**
     * @param $_order
     *
     * @return array|mixed
     */
    private function orderMap($_order)
    {
        if (!$_order) {
            $_order['id'] = 'DESC';
        }

        $order = [];

        if (isset($_order['id'])) {
            $order['id'] = $_order['id'];
        }

        if (isset($_order['created'])) {
            $order['createdAt'] = $_order['created'];
        }

        if (isset($_order['date_from'])) {
            $order['dateFrom'] = $_order['date_from'];
        }

        if (isset($_order['date_to'])) {
            $order['dateTo'] = $_order['date_to'];
        }

        if (isset($_order['completed'])) {
            $order['completed'] = $_order['completed'];
        }

        if (isset($_order['couteragent_id'])) {
            $order['client']['id'] = $_order['couteragent_id'];
        }

        return $order;
    }


    /**
     * @param $value
     *
     * @return \DateTime
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
}