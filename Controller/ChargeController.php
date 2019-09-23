<?php

namespace LaxCorp\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use AppApiBundle\Model\InputCharge;
use BillingApiBundle\Services\Api\Api as BillingApi;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\RouteResource("Charge", pluralize=false)
 */
class ChargeController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Списание (charge)"},
     *     summary="All billing AccountOperation",
     *     @SWG\Parameter(
     *         name="_limit",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_page",
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
     *         name="closed",
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
     *         name="couteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="body",
     *         description="SUBSCRIPTION | OVERUSE_CLICKS | REFILL | MONEYBACK | PACKET_ACQUISITION",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="description",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="tariff_name",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="multiplier",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="clicks_count",
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
     *         response="404",
     *         description="Returned when the invoice is not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     *
     * @Rest\QueryParam(name="_limit",  requirements="\d+", default=2, nullable=true, strict=true)
     * @Rest\QueryParam(name="_page", requirements="\d+", default=0, nullable=true, strict=true)
     * @Rest\QueryParam(name="_order", nullable=true, description="Default: _order[id]=DESC")
     * @Rest\View()
     *
     * @param Request               $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return object
     */
    public function cgetAction(Request $request, ParamFetcherInterface $paramFetcher)
    {

        $_limit  = $paramFetcher->get('_limit');
        $_page = $paramFetcher->get('_page');
        $_order  = $paramFetcher->get('_order');

        $order = $this->orderMap($_order);

        /** @var BillingApi $billing_api */
        $billing_api = $this->getBillingApi();

        /** @var InputCharge $input */
        $input  = $this->requestMap(InputCharge::class, $request->query->all());
        $fields = $this->searchMap($input);

        $result =  $billing_api->searchAccountOperations($fields, $_limit, $_page, $order);

        return $result->items;

    }

    /**
     * @Operation(
     *     tags={"Списание (charge)"},
     *     summary="Single billing AccountOperation by id",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     *
     * @Rest\Route(requirements={ "id": "\d+" })
     * @param $id
     *
     */
    public function getAction($id)
    {
        /** @var BillingApi $billing_api */
        $billing_api = $this->getBillingApi();

        $result =  $billing_api->searchAccountOperations(['id'=>$id], 1, 0, []);

        if(!$result->items){
            throw new NotFoundHttpException();
        }

        return $result->items[0];
    }


    /**
     * @param InputCharge $input
     *
     * @return array
     */
    private function searchMap(InputCharge $input)
    {
        $fields = [];

        if ($input->getCreated() !== null) {
            $fields['created'] = $input->getCreated();
        }

        if ($input->getClosed() !== null) {
            $fields['closed'] = $input->getClosed();
        }

        if ($input->getAmount() !== null) {
            $fields['amount'] = $input->getAmount();
        }

        if ($input->getCouteragentId() !== null) {
            $fields['account.id'] = $input->getCouteragentId();
        }

        $type = $input->getType();
        if ($type !== null) {
            $fields['reason'] = (!preg_match('/^=/', $type)) ? '=' . $type : $type;
        }

        if ($input->getDescription() !== null) {
            $fields['description'] = $input->getDescription();
        }

        if ($input->getTariffName() !== null) {
            $fields['tariffName'] = $input->getTariffName();
        }

        if ($input->getMultiplier() !== null) {
            $fields['multiplier'] = $input->getMultiplier();
        }


        if ($input->getClicksCount() !== null) {
            $fields['clicksCount'] = $input->getClicksCount();
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
            $order['created'] = $_order['created'];
        }

        if (isset($_order['amount'])) {
            $order['amount'] = $_order['amount'];
        }

        if (isset($_order['couteragent_id'])) {
            $order['account.id'] = $_order['couteragent_id'];
        }

        if (isset($_order['tariff_name'])) {
            $order['tariffName'] = $_order['tariff_name'];
        }

        if (isset($_order['multiplier'])) {
            $order['multiplier'] = $_order['multiplier'];
        }

        if (isset($_order['clicks_count'])) {
            $order['clicksCount'] = $_order['clicks_count'];
        }

        return $order;
    }

    /**
     * @return object Api
     */
    private function getBillingApi()
    {
        return $this->get('billing_api.api');
    }

}