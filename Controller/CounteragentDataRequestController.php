<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Model\SearchCompanyChangeRequest;
use App\Entity\Client;
use App\Entity\CompanyChangeRequest;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @REST\RouteResource("CounteragentDataRequest", pluralize=false)
 */
class CounteragentDataRequestController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Запрос на смену реквизитов юридического лица (counteragent_data_request)"},
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
     *         name="counteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="inn",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="kpp",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="reg_number",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="tax_number",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="juridical_address",
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
     *     @SWG\Parameter(
     *         name="description",
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
     * @REST\Route(path="counteragent_data_request")
     * @REST\QueryParam(name="_limit",  requirements="\d+", default=2, nullable=true, strict=true)
     * @REST\QueryParam(name="_offset", requirements="\d+", default=0, nullable=true, strict=true)
     * @Rest\QueryParam(name="_order", nullable=true, description="Default: _order[id]=DESC")
     * @REST\View()
     * @param Request               $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function cgetAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $_limit  = $paramFetcher->get('_limit');
        $_offset = $paramFetcher->get('_offset');
        $_order  = $paramFetcher->get('_order');
        $_order  = $this->orderMap($_order);

        $repository = $this->getRepository(CompanyChangeRequest::class);

        /** @var SearchCompanyChangeRequest $input */
        $input  = $this->requestMap(SearchCompanyChangeRequest::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $_order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Запрос на смену реквизитов юридического лица (counteragent_data_request)"},
     *     summary="",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when found"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when the CounteragentDataRequest is not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     * @REST\Route(path="counteragent_data_request/{id}", requirements={ "id": "\d+" })
     * @param $id integer
     *
     * @return Client|null|object
     */
    public function getAction($id)
    {
        $result = $this->findOneBy(['id' => (integer)$id]);

        if (!$result) {
            throw $this->createNotFoundException();
        }

        return $result;
    }

    /**
     * @Operation(
     *     tags={"Запрос на смену реквизитов юридического лица (counteragent_data_request)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="id",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="counteragent_id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="counteragent",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="object (Client)")
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="inn",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="kpp",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="reg_number",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="tax_number",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="juridical_address",
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
     *         @SWG\Schema(type="DateTime")
     *     ),
     *     @SWG\Parameter(
     *         name="description",
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
     * )
     * @REST\Route(path="counteragent_data_request/{id}", requirements={ "id": "\d+" })
     * @REST\View()
     * @param         $id
     * @param Request $request
     *
     * @return object
     * @throws \Doctrine\DBAL\DBALException
     */
    public function patchAction($id, Request $request)
    {

        $conflicts = [];
        $invalid   = [];

        $entity = $this->findOneBy(['id' => (integer)$id]);

        $requestFields = $request->request->all();

        /** @var CompanyChangeRequest $input */
        $input = $this->requestMap(CompanyChangeRequest::class, $requestFields);

        $entityUpdated = $this->patchClass($entity, $input, $requestFields);

        $violations = $this->get('validator')->validate($entityUpdated);

        if ($violations->count() != 0) {

            $accessor = PropertyAccess::createPropertyAccessor();

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $constraint = $violation->getConstraint();
                $fields     = $constraint->fields;
                $key        = $violation->getPropertyPath();
                $value      = $violation->getInvalidValue();
                $root       = $violation->getRoot();

                if ($constraint instanceof UniqueEntity) {

                    $param         = [];
                    $relatedFields = explode('.', $key);
                    $key           = $relatedFields[0];

                    if (is_array($fields)) {
                        foreach ($fields as $field) {
                            $param[$key][$field] = '=' . $accessor->getValue($root, $key . '.' . $field);
                        }
                    } else {
                        $param = [$key => '=' . $value];
                    }
                    $entityRepository = $this->getRepository(CompanyChangeRequest::class);
                    $matcherResult    = $this->getMatcher()->matching($entityRepository, $param);
                    $record           = $matcherResult->getList();

                    if (is_array($fields)) {
                        foreach ($fields as $field) {
                            $conflicts[] = [
                                'field'         => $field,
                                'value'         => $accessor->getValue($root, $key . '.' . $field),
                                'message'       => $violation->getMessage(),
                                'conflict_with' => $record
                            ];
                        }
                    } else {
                        $conflicts[] = [
                            'field'         => $fields,
                            'value'         => $value,
                            'message'       => $violation->getMessage(),
                            'conflict_with' => $record
                        ];
                    }
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
        $em->persist($entityUpdated);
        $em->flush();

        return $entityUpdated;

    }

    /**
     * @param CompanyChangeRequest $entity
     * @param CompanyChangeRequest $input
     *
     * @param array                $requestFields
     *
     * @return CompanyChangeRequest
     */
    private function patchClass(CompanyChangeRequest $entity, CompanyChangeRequest $input, array $requestFields)
    {
        if (array_key_exists('completed', $requestFields)) {
            $entity->setCompleted($input->getCompleted());
        }

        if (array_key_exists('description', $requestFields)) {
            $entity->setDescription($input->getDescription());
        }

        return $entity;
    }


    /**
     * @param SearchCompanyChangeRequest $input
     *
     * @return array
     */
    private function searchMap(SearchCompanyChangeRequest $input)
    {

        $fields = [];

        if ($input->getCounteragentId() !== null) {
            $fields['client']['accountId'] = $input->getCounteragentId();
        }

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getCountryCode() !== null) {
            $fields['countryCode'] = $input->getCountryCode();
        }

        if ($input->getName() !== null) {
            $fields['name'] = $input->getName();
        }

        if ($input->getInn() !== null) {
            $fields['inn'] = $input->getInn();
        }

        if ($input->getKpp() !== null) {
            $fields['kpp'] = $input->getKpp();
        }

        if ($input->getRegNumber() !== null) {
            $fields['regNumber'] = $input->getRegNumber();
        }

        if ($input->getTaxNumber() !== null) {
            $fields['taxNumber'] = $input->getTaxNumber();
        }

        if ($input->getLegalAddress() !== null) {
            $fields['legalAddress'] = $input->getLegalAddress();
        }

        if ($input->getCompleted() !== null) {
            $fields['completed'] = $input->getCompleted();
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
            $order['id'] = $_order['id'];
        }

        if (isset($_order['counteragent_id'])) {
            $order['client']['id'] = $_order['counteragent_id'];
        }

        if (isset($_order['created'])) {
            $order['createdAt'] = $_order['created'];
        }

        if (isset($_order['country_code'])) {
            $order['countryCode'] = $_order['country_code'];
        }

        if (isset($_order['name'])) {
            $order['name'] = $_order['name'];
        }

        if (isset($_order['inn'])) {
            $order['inn'] = $_order['inn'];
        }

        if (isset($_order['kpp'])) {
            $order['kpp'] = $_order['kpp'];
        }

        if (isset($_order['reg_number'])) {
            $order['regNumber'] = $_order['reg_number'];
        }

        if (isset($_order['tax_number'])) {
            $order['taxNumber'] = $_order['tax_number'];
        }

        if (isset($_order['legalAddress'])) {
            $order['legalAddress'] = $_order['legalAddress'];
        }

        if (isset($_order['completed'])) {
            $order['completed'] = $_order['completed'];
        }

        if (isset($_order['description'])) {
            $order['description'] = $_order['description'];
        }

        return $order;
    }

    /**
     * @param $param
     *
     * @return CompanyChangeRequest|null|object
     */
    private function findOneBy($param)
    {
        return $this->getRepository(CompanyChangeRequest::class)->findOneBy($param);
    }
}