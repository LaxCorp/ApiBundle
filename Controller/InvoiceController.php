<?php
namespace LaxCorp\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Invoice;
use AppApiBundle\Model\InputInvoice;

/**
 * @Rest\RouteResource("Invoice", pluralize=false)
 */
class InvoiceController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Счет (invoice)"},
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
     *         name="created",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="paid_amount",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="couteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
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

        $repository = $this->getRepository(Invoice::class);

        /** @var InputInvoice $input */
        $input  = $this->requestMap(InputInvoice::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Счет (invoice)"},
     *     summary="",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="App\Entity\Invoice"))
     *     )
     * )
     *
     *
     * @Rest\Route(requirements={ "id": "\d+" })
     * @param $id
     *
     * @return Invoice|null|object
     */
    public function getAction($id)
    {

        $invoice = $this->getRepository(Invoice::class)->find((integer)$id);
        if (!$invoice) {
            throw $this->createNotFoundException('invoice not found');
        }

        return $invoice;
    }


    /**
     * @param InputInvoice $input
     *
     * @return array
     */
    private function searchMap(InputInvoice $input)
    {
        $fields = [];

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getAmount() !== null) {
            $fields['amount'] = $input->getAmount();
        }

        if ($input->getPaidAmount() !== null) {
            $fields['paidAmount'] = $input->getPaidAmount();
        }

        if ($input->getCouteragentId() !== null) {
            $fields['client']['accountId'] = $input->getCouteragentId();
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

        if (isset($_order['amount'])) {
            $order['amount'] = $_order['amount'];
        }

        if (isset($_order['paid_amount'])) {
            $order['paidAmount'] = $_order['paid_amount'];
        }

        if (isset($_order['couteragent_id'])) {
            $order['client']['id'] = $_order['couteragent_id'];
        }

        return $order;
    }


}