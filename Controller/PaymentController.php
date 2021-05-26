<?php

namespace LaxCorp\ApiBundle\Controller;

use App\Entity\BatchPayment;
use LaxCorp\ApiBundle\Model\InputPatchPayment;
use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Payment;
use LaxCorp\ApiBundle\Model\InputPayment;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
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
 * @Rest\RouteResource("Payment", pluralize=false)
 */
class PaymentController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Оплата (payment)"},
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
     *         name="invoice_id",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="comission",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="couteragent_id",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="payment_type",
     *         in="query",
     *         description="BANK | ONLINE",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         description="REFILL | MONEYBACK",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="description",
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

        $repository = $this->getRepository(Payment::class);

        /** @var InputPayment $input */
        $input  = $this->requestMap(InputPayment::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Оплата (payment)"},
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
        $result = $this->findOneBy(['operation' => (integer)$id]);
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * @Operation(
     *     tags={"Оплата (payment)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="created",
     *         in="formData",
     *         description="readOnly",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="invoice_id",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="comission",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="couteragent_id",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="payment_type",
     *         in="formData",
     *         description="BANK | ONLINE",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="REFILL | MONEYBACK",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="description",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
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
        $conflicts = [];
        $invalid   = [];

        $payment = new Payment();

        /** @var InputPayment $input */
        $input = $this->requestMap(InputPayment::class, $request->request->all());

        $paymentUpdated = $this->postClass($payment, $input);
        $paymentUpdated->setBatchPayment(new BatchPayment());

        if ($this->invoiceNotBelongsClient($paymentUpdated)) {
            $invalid[] = [
                'field'   => 'invoice_id',
                'value'   => $paymentUpdated->getInvoice(),
                'message' => 'Invoice not belongs this client'
            ];

            return $this->errorView($conflicts, $invalid);
        }

        if (!$paymentUpdated->getInvoice() && !$paymentUpdated->getClient()) {
            $invalid[] = [
                'field'   => 'invoice_id',
                'value'   => $paymentUpdated->getInvoice(),
                'message' => 'Required: invoice_id || couteragent_id'
            ];
            $invalid[] = [
                'field'   => 'couteragent_id',
                'value'   => $paymentUpdated->getInvoice(),
                'message' => 'Required: couteragent_id || invoice_id'
            ];

            return $this->errorView($conflicts, $invalid);
        }

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
            return $this->billingNotFound($paymentUpdated->getCouteragentId());
        }

        $view = $this->view($paymentUpdated, Response::HTTP_CREATED);

        return $this->handleView($view);
    }

    /**
     * @Operation(
     *     tags={"Оплата (payment)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="invoice_id",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer"
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

        $payment = $this->findOneBy(['operation' => (integer)$id]);

        if (!$payment) {
            throw new NotFoundHttpException();
        }

        $requestFields = $request->request->all();

        /** @var InputPatchPayment $input */
        $input = $this->requestMap(InputPatchPayment::class, $requestFields);

        $paymentUpdated = $this->patchClass($payment, $input, $requestFields);

        if ($this->invoiceNotBelongsClient($paymentUpdated)) {
            $invalid[] = [
                'field'   => 'invoice_id',
                'value'   => $paymentUpdated->getInvoice(),
                'message' => 'Invoice not belongs this client'
            ];

            return $this->errorView($conflicts, $invalid);
        }

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
            return $this->billingNotFound($paymentUpdated->getCouteragentId());
        }

        return $paymentUpdated;
    }

    /**
     * @param Payment $payment
     *
     * @return bool
     */
    private function invoiceNotBelongsClient(Payment $payment)
    {
        $invoice = $payment->getInvoice();
        $client  = $payment->getClient();
        if ($invoice && $client && $invoice->getClient() !== $client) {
            return true;
        }

        return false;
    }

    /**
     * @param Payment           $payment
     * @param InputPatchPayment $input
     *
     * @param array             $requestFields
     *
     * @return Payment
     */
    private function patchClass(Payment $payment, InputPatchPayment $input, array $requestFields)
    {
        if (array_key_exists('invoice_id', $requestFields)) {
            $invoice = $this->getInvoice($input->getInvoiceId());
            $payment->setInvoice($invoice);
        }

        if (array_key_exists('uuid1c', $requestFields)) {
            $payment->setUuid1c($input->getUuid1c());
        }

        return $payment;
    }


    /**
     * @param $id
     *
     * @return Client
     */
    private function getClient($id)
    {
        $client = $this->getRepository(Client::class)->findOneBy(['counteragentId' => (integer)$id]);
        if (!$client) {
            throw $this->createNotFoundException('client not found');
        }

        return $client;
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
     * @return Payment
     */
    private function findOneBy($param)
    {
        return $this->getRepository(Payment::class)->findOneBy($param);
    }

    /**
     * @param Payment      $payment
     * @param InputPayment $input
     *
     * @return Payment
     */
    private function postClass(Payment $payment, InputPayment $input)
    {

        if ($input->getUuid1c() !== null) {
            $payment->setUuid1c($input->getUuid1c());
        }

        if ($input->getInvoiceId() !== null) {
            $invoice = $this->getInvoice($input->getInvoiceId());

            $payment->setInvoice($invoice);
        }

        if ($input->getAmount() !== null) {
            $payment->setAmount($input->getAmount());
        }

        if ($input->getComission() !== null) {
            $payment->setAmountCommission($input->getComission());
        }

        if ($input->getCouteragentId() !== null) {
            $client = $this->getClient($input->getCouteragentId());
            $payment->setClient($client);
        }

        $payment->setPaymentType($input->getPaymentType());
        $payment->setType($input->getType());
        $payment->setDescription($input->getDescription());

        return $payment;
    }

    /**
     * @param InputPayment $input
     *
     * @return array
     */
    private function searchMap(InputPayment $input)
    {
        $fields = [];

        if ($input->getUuid1c() !== null) {
            $fields['uuid1c'] = $input->getUuid1c();
        }

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getInvoiceId() !== null) {
            $fields['invoice']['id'] = $input->getInvoiceId();
        }

        if ($input->getAmount() !== null) {
            $fields['amount'] = $input->getAmount();
        }

        if ($input->getComission() !== null) {
            $fields['amount_comission'] = $input->getComission();
        }

        if ($input->getCouteragentId() !== null) {
            $fields['client']['counteragentId'] = $input->getCouteragentId();
        }

        if ($input->getPaymentType() !== null) {
            $fields['paymentType'] = $input->getPaymentType();
        }

        if ($input->getType() !== null) {
            $fields['type'] = $input->getType();
        }

        if ($input->getDescription() !== null) {
            $fields['description'] = $input->getDescription();
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
            $order['operation'] = $_order['id'];
        }

        if (isset($_order['created'])) {
            $order['createdAt'] = $_order['created'];
        }

        if (isset($_order['amount'])) {
            $order['amount'] = $_order['amount'];
        }

        if (isset($_order['couteragent_id'])) {
            $order['client']['id'] = $_order['couteragent_id'];
        }

        if (isset($_order['invoice_id'])) {
            $order['invoice']['id'] = $_order['invoice_id'];
        }

        return $order;
    }

    /**
     * @param $accountId
     *
     * @return Response
     */
    private function billingNotFound($accountId)
    {

        $invalid[] = [
            'field'   => 'counteragent_id',
            'value'   => $accountId,
            'message' => 'Billing account not found'
        ];

        $code = Response::HTTP_NOT_FOUND;

        $view = $this->view([
            'error' => [
                'code'    => $code,
                'message' => '404 Not Found',
                'fields'  => $invalid
            ]
        ], $code);

        return $this->handleView($view);

    }

}
