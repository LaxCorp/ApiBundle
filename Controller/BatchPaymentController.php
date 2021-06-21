<?php

namespace LaxCorp\ApiBundle\Controller;

use App\Entity\BatchPayment;
use App\Entity\RemoteAccount;
use LaxCorp\ApiBundle\Model\InputBatchPayment;
use App\Entity\Invoice;
use App\Entity\Payment;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use LaxCorp\ApiBundle\Model\SearchBatchPayment;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Rest\RouteResource("BatchPayments", pluralize=false)
 */
class BatchPaymentController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Оплата на несколько account"},
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
     *         name="uuid1c",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="created",
     *         in="query",
     *         description="readOnly",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="updated",
     *         in="query",
     *         description="readOnly",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="comment",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
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
     *
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

        $repository = $this->getRepository(BatchPayment::class);

        /** @var SearchBatchPayment $input */
        $input  = $this->requestMap(SearchBatchPayment::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Оплата на несколько account"},
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
     *         description="Returned when the payment is not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     *
     * @Rest\Route(requirements={ "id": "\d+" })
     * @param $id
     *
     * @return Response|Payment
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
     *     tags={"Оплата на несколько account"},
     *     summary="",
     *     @SWG\Parameter(
     *        name="JSON body",
     *        in="body",
     *        description="json request object",
     *        required=true,
     *        @SWG\Schema(
     *            type="object",
     *            ref=@Model(type=InputBatchPayment::class, groups={"PostAction"})
     *        )
     *      ),
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
        $conflicts = [];
        $invalid   = [];

        /** @var InputBatchPayment $inputBatchPayment */
        $inputBatchPayment = $this->requestMap(InputBatchPayment::class, $request->request->all());
        $batchPayment      = $this->postClass(new BatchPayment(), $inputBatchPayment);

        $violations = $this->validator->validate($batchPayment);

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
        try {
            $em->persist($batchPayment);
            $em->flush();
        } catch (ResourceNotFoundException $e) {
            return $e;
        }

        $view = $this->view($batchPayment, Response::HTTP_CREATED);

        return $this->handleView($view);
    }

    /**
     * @Operation(
     *     tags={"Оплата на несколько account"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
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
    public function patchAction($id, Request $request)
    {
        $conflicts = [];
        $invalid   = [];

        $batchPayment = $this->findOneBy(['id' => (integer)$id]);

        if (!$batchPayment) {
            throw new NotFoundHttpException();
        }

        $requestFields = $request->request->all();

        /** @var InputBatchPayment $input */
        $input = $this->requestMap(InputBatchPayment::class, $requestFields);

        $paymentUpdated = $this->patchClass($batchPayment, $input, $requestFields);

        $violations = $this->validator->validate($paymentUpdated);

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

        try {
            $em->persist($paymentUpdated);
            $em->flush();
        } catch (ResourceNotFoundException $e) {
            return $e;
        }

        return $paymentUpdated;
    }

    /**
     * @param BatchPayment      $batchPayment
     * @param InputBatchPayment $input
     *
     * @param array             $requestFields
     *
     * @return BatchPayment
     */
    private function patchClass(BatchPayment $batchPayment, InputBatchPayment $input, array $requestFields)
    {
        if (array_key_exists('uuid1c', $requestFields)) {
            $batchPayment->setUuid1c($input->getUuid1c());
        }

        return $batchPayment;
    }

    /**
     * @param $id
     *
     * @return RemoteAccount
     */
    private function getRemoteAccount($remoteId)
    {
        $remoteAccount = $this->getRepository(RemoteAccount::class)->findOneBy(['remoteId' => (integer)$remoteId]);
        if (!$remoteAccount) {
            throw $this->createNotFoundException('site account not found');
        }

        return $remoteAccount;
    }

    /**
     * @param $id
     *
     * @return Invoice
     */
    private function getInvoice($id)
    {
        $invoice = $this->getRepository(Invoice::class)->findOneBy(['id' => (integer)$id]);
        if (!$invoice) {
            throw $this->createNotFoundException('invoice not found');
        }

        return $invoice;
    }

    /**
     * @param $param
     *
     * @return BatchPayment
     */
    private function findOneBy($param)
    {
        return $this->getRepository(BatchPayment::class)->findOneBy($param);
    }

    /**
     * @param BatchPayment      $payment
     * @param InputBatchPayment $input
     *
     * @return BatchPayment
     */
    private function postClass(BatchPayment $batchPayment, InputBatchPayment $input)
    {
        $inputPayments = $input->getPayments();

        foreach ($inputPayments as $inputPayment) {
            $payment = new Payment();

            $remoteId      = $inputPayment->getAccountId();
            $remoteAccount = $this->getRemoteAccount($remoteId);
            $payment->setRemoteAccount($remoteAccount);

            $client = $remoteAccount->getClient();
            $payment->setClient($client);

            if (!empty($inputPayment->getInvoiceId())) {
                $invoiceId            = $inputPayment->getInvoiceId();
                $invoice              = $this->getInvoice($invoiceId);
                $invoiceRemoteAccount = $invoice->getRemoteAccount();

                if ($invoiceRemoteAccount->getRemoteId() !== $remoteId) {
                    throw $this->createNotFoundException("Invoice: $invoiceId - does not belong to the client");
                }

                $payment->setInvoice($invoice);
            }

            $payment->setAmount($inputPayment->getAmount());

            if (!empty($inputPayment->getComission())) {
                $payment->setAmountCommission($inputPayment->getComission());
            }

            if (!empty($input->getUuid1c())) {
                $batchPayment->setUuid1c($input->getUuid1c());
            }

            $payment->setPaymentType($inputPayment->getPaymentType());
            $payment->setType($inputPayment->getType());
            $payment->setDescription($inputPayment->getDescription());
            $payment->setBatchPayment($batchPayment);

            $batchPayment->addPayment($payment);
        }

        return $batchPayment;
    }

    /**
     * @return array
     */
    private function searchMap(SearchBatchPayment $input)
    {
        $fields = [];

        if ($input->getUuid1c() !== null) {
            $fields['uuid1c'] = $input->getUuid1c();
        }

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getUpdatedAt() !== null) {
            $fields['updatedAt'] = $input->getUpdatedAt();
        }

        if ($input->getComment() !== null) {
            $fields['comment'] = $input->getComment();
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

        if (isset($_order['updated'])) {
            $order['updatedAt'] = $_order['updated'];
        }

        return $order;
    }

}
