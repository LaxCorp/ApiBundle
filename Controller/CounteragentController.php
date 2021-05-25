<?php

namespace LaxCorp\ApiBundle\Controller;

use App\Entity\Client;
use App\Entity\RemoteAccount;
use LaxCorp\ApiBundle\Helper\DoctrineMatcherResult;
use LaxCorp\ApiBundle\Model\InputCounteragent;
use App\Entity\Company;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @REST\RouteResource("Counteragent", pluralize=false)
 */
class CounteragentController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Поиск контрагента (Search counteragent)"},
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
     *         name="uuid1c",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="inn",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="kpp",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="reg_number",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="tax_number",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="juridical_address",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="post_address",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_name",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_email",
     *         in="query",
     *         description="ReadOnly",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_phone",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="data_checked",
     *         in="query",
     *         description="details of the organization filled",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="waspayment",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="boolean"
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
     * @REST\QueryParam(name="_limit",  requirements="\d+", default=2, nullable=true, strict=true)
     * @REST\QueryParam(name="_offset", requirements="\d+", default=0, nullable=true, strict=true)
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
        $_order  = ['createdAt' => 'DESC'];

        $repository = $this->getRepository(Client::class);

        /** @var InputCounteragent $input */
        $input  = $this->requestMap(InputCounteragent::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $_order, $_offset, $_limit);

        return $this->createClientsViewByMatcher($matcherResult, 200);
    }

    /**
     * @param DoctrineMatcherResult $matcherResult
     * @param null                  $statusCode
     * @param array                 $headers
     *
     * @return Request
     */
    private function createClientsViewByMatcher(
        DoctrineMatcherResult $matcherResult,
        $statusCode = null,
        array $headers = []
    ): Response {
        $offset = $matcherResult->getFirstResult();
        $limit  = $matcherResult->getMaxResults();

        /** @var Client[] $clients */
        $clients = $matcherResult->getList();

        foreach ($clients as $client) {
            $remoteAccounts = $client->getRemoteAccounts();
            foreach ($remoteAccounts as $remoteAccount) {
                $this->prepareRemoteAccount($remoteAccount);
            }
        }

        $view = $this->view($clients, $statusCode, $headers);
        $this->setContentRangeHeader($view, $offset, $limit, $matcherResult->getTotal());

        return $this->handleView($view);
    }


    /**
     * @Operation(
     *     tags={"Получить данные контрагента (Get counteragent by id)"},
     *     summary="",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @REST\Route(requirements={ "id": "\d+" })
     * @param $id integer
     *
     * @return Client|null|object
     */
    public function getAction($id)
    {
        $client = $this->getClient($id);
        $remoteAccounts = $client->getRemoteAccounts();
        foreach ($remoteAccounts as $remoteAccount) {
            $this->prepareRemoteAccount($remoteAccount);
        }
        return $client;
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
     * @Operation(
     *     tags={"Частично обновить данные контрагента (PATH counteragent)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
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
     *         name="inn",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="kpp",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="reg_number",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="tax_number",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="juridical_address",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="post_address",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_email",
     *         in="formData",
     *         description="ReadOnly",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_phone",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="data_checked",
     *         in="formData",
     *         description="details of the organization filled",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="waspayment",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @REST\Route(requirements={ "id": "\d+" })
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

        $client = $this->getClient($id);

        $requestFields = $request->request->all();

        /** @var InputCounteragent $input */
        $input = $this->requestMap(InputCounteragent::class, $requestFields);

        $clientUpdated = $this->patchClass($client, $input, $requestFields);

        $violations = $this->validator->validate($clientUpdated);

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
                    $entityRepository = $this->getRepository(Client::class);
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
                        'field'   => $this->keyNameFix($key),
                        'value'   => $value,
                        'message' => $violation->getMessage()
                    ];
                }
            }

            return $this->errorView($conflicts, $invalid);
        }

        // отключить создание событий для данного update
        $this->counteragentUpdateSubscriber->setDisabled(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($clientUpdated);
        $em->flush();

        $remoteAccounts = $clientUpdated->getRemoteAccounts();
        foreach ($remoteAccounts as $remoteAccount) {
            $this->prepareRemoteAccount($remoteAccount);
        }

        return $clientUpdated;
    }

    /**
     * @param Client            $client
     * @param InputCounteragent $input
     *
     * @param array             $requestFields
     *
     * @return Client
     * @internal param array $fields
     *
     * @internal param array $isset
     *
     * @internal param array $fields
     */
    private function patchClass(Client $client, InputCounteragent $input, array $requestFields)
    {
        $company = $client->getCompany();

        if (!$company) {
            $client->setCompany(new Company());
            $company = $client->getCompany();
        }

        $user = $client->getUser();
        if (!$user) {
            $client->setUser(new User());
            $user = $client->getUser();
        }

        if (array_key_exists('waspayment', $requestFields)) {
            $client->setWaspayment($input->isWaspayment());
        }

        if (array_key_exists('uuid1c', $requestFields)) {
            $client->setUuid1c($input->getUuid1c());
        }

        if (array_key_exists('name', $requestFields)) {
            $company->setName($input->getName());
        }

        if (array_key_exists('inn', $requestFields)) {
            $company->setInn($input->getInn());
        }

        if (array_key_exists('kpp', $requestFields)) {
            $company->setKpp($input->getKpp());
        }

        if (array_key_exists('juridical_address', $requestFields)) {
            $company->setLegalAddress($input->getJuridicalAddress());
        }

        if (array_key_exists('post_address', $requestFields)) {
            $company->setPostalAddress($input->getPostAddress());
        }

        if (array_key_exists('contact_name', $requestFields)) {
            $user->setName($input->getContactName());
        }

        if (array_key_exists('contact_phone', $requestFields)) {
            $user->setPhone($input->getContactPhone());
        }

        if (array_key_exists('data_checked', $requestFields)) {
            $company->setChecked($input->isDataChecked());
        }

        if (array_key_exists('country_code', $requestFields)) {
            $company->setCountryCode($input->getCountryCode());
        }

        if (array_key_exists('reg_number', $requestFields)) {
            $company->setRegNumber($input->getRegNumber());
        }

        if (array_key_exists('tax_number', $requestFields)) {
            $company->setTaxNumber($input->getTaxNumber());
        }

        return $client;
    }

    /**
     * @param InputCounteragent $input
     *
     * @return array
     */
    private function searchMap(InputCounteragent $input)
    {
        $fields = [];

        if ($input->isWaspayment() !== null) {
            $fields['waspayment'] = $input->isWaspayment();
        }

        if ($input->getUuid1c() !== null) {
            $fields['uuid1c'] = $input->getUuid1c();
        }

        if ($input->getCountryCode() !== null) {
            $fields['company']['countryCode'] = $input->getCountryCode();
        }

        if ($input->getRegNumber() !== null) {
            $fields['company']['regNumber'] = $input->getRegNumber();
        }

        if ($input->getTaxNumber() !== null) {
            $fields['company']['taxNumber'] = $input->getTaxNumber();
        }

        if ($input->getName() !== null) {
            $fields['company']['name'] = $input->getName();
        }

        if ($input->getInn() !== null) {
            $fields['company']['inn'] = $input->getInn();
        }

        if ($input->getKpp() !== null) {
            $fields['company']['kpp'] = $input->getKpp();
        }

        if ($input->getJuridicalAddress() !== null) {
            $fields['company']['legalAddress'] = $input->getJuridicalAddress();
        }

        if ($input->getPostAddress() !== null) {
            $fields['company']['postalAddress'] = $input->getPostAddress();
        }

        if ($input->getContactName() !== null) {
            $fields['user']['name'] = $input->getContactName();
        }

        if ($input->getContactEmail() !== null) {
            $fields['user']['email'] = $input->getContactEmail();
        }

        if ($input->getContactPhone() !== null) {
            $fields['user']['phone'] = $input->getContactPhone();
        }

        if ($input->isDataChecked() !== null) {
            $fields['company']['checked'] = $input->isDataChecked();
        }

        return $fields;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function keyNameFix($key)
    {
        return preg_replace('/company\./', '', $key);
    }

}
