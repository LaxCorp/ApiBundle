<?php

namespace LaxCorp\ApiBundle\Controller;

use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use LaxCorp\BillingPartnerBundle\Query\SearchQuery;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use LaxCorp\ApiBundle\Model\InputCharge;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use LaxCorp\BillingPartnerBundle\Model\AccountOperation;

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
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="closed",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         description="SUBSCRIPTION | OVERUSE_CLICKS | REFILL | MONEYBACK | PACKET_ACQUISITION",
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
     *     @SWG\Parameter(
     *         name="tariff_name",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="multiplier",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="clicks_count",
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
        $_limit = $paramFetcher->get('_limit');
        $_page  = $paramFetcher->get('_page');
        $_order = $paramFetcher->get('_order');

        $order = $this->orderMap($_order);

        /** @var InputCharge $input */
        $input  = $this->requestMap(InputCharge::class, $request->query->all());
        $fields = $this->searchMap($input);

        $result = $this->searchAccountOperations($fields, $_limit, $_page, $order);

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
        $result = $this->accountOperationHelper->findOneById($id);
        if (!$result || !$result->getId()) {
            throw new NotFoundHttpException();
        }

        return (array)$this->chargeOut($result);
    }

    /**
     * @param array    $fields
     * @param int      $limit
     * @param int      $page
     * @param string[] $order
     *
     * @return object
     */
    private function searchAccountOperations($fields = [], $limit = 10, $page = 0, $order = ['id' => 'DESC'])
    {
        $return = (object)[
            'count' => 0,
            'page'  => $page,
            'items' => [],
            'pages' => []
        ];

        /** @var EntityRepository $repository */
        $repository = $this->getDoctrine()->getRepository('ApiBundle:AccountOperation');

        $searchQuery = new SearchQuery();
        $searchQuery->setSearch($this->accountOperationHelper->createSearch($repository, $fields));
        $searchQuery->setSize($limit);
        $searchQuery->setPage((int)$page);

        $return->count = $this->accountOperationHelper->getCount($searchQuery);

        if (!$return->count) {
            return $return;
        }

        $pages = $return->count / $limit;

        if ($pages > 1) {
            $pages_count = ceil($pages);
            for ($i = 0; $i < $pages_count; $i++) {
                $return->pages[] = $i + 1;
            }
        }

        $searchQuery->setSort($this->accountOperationHelper->createSort($order));

        $result = $this->accountOperationHelper->find($searchQuery);

        foreach ($result as $accountOperations) {
            $return->items[] = (array)$this->chargeOut($accountOperations);
        }

        return $return;
    }

    /**
     * @param AccountOperation $charge
     *
     * @return object
     */
    private function chargeOut(AccountOperation $charge)
    {
        $o               = (object)[];
        $o->id           = $charge->getId();
        $o->created      = $charge->getCreated();
        $o->closed       = $charge->getClosed();
        $o->amount       = $charge->getAmount();
        $o->account_id   = $charge->getAccount()->getId();
        $o->type         = $charge->getReason();
        $o->tariff_name  = $charge->getTariffName();
        $o->clicks_count = $charge->getClicksCount();
        $o->multiplier   = $charge->getMultiplier();
        $o->description  = $charge->getDescription();

        return $o;
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

        if (isset($_order['account_id'])) {
            $order['account.id'] = $_order['account_id'];
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

}
