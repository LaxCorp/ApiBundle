<?php

namespace LaxCorp\ApiBundle\Controller;

use App\Entity\Client;
use App\Entity\Profiles;
use App\Entity\RemoteAccount;
use LaxCorp\ApiBundle\Helper\DoctrineMatcherResult;
use LaxCorp\ApiBundle\Model\InputAccount;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use LaxCorp\BillingPartnerBundle\Helper\CustomerHelper;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @REST\RouteResource("Account", pluralize=false)
 */
class AccountController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Поиск аккаунтов (Search account)"},
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
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function cgetAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $_limit  = $paramFetcher->get('_limit');
        $_offset = $paramFetcher->get('_offset');
        $_order  = ['createdAt' => 'DESC'];

        $repository = $this->getRepository(RemoteAccount::class);

        /** @var InputAccount $input */
        $input  = $this->requestMap(InputAccount::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $_order, $_offset, $_limit);

        return $this->createProfilesViewByMatcher($matcherResult, 200, []);
    }

    /**
     * @param DoctrineMatcherResult $matcherResult
     * @param null                  $statusCode
     * @param array                 $headers
     *
     * @return Request
     */
    private function createProfilesViewByMatcher(
        DoctrineMatcherResult $matcherResult,
        $statusCode = null,
        array $headers = []
    ): Response {
        $offset = $matcherResult->getFirstResult();
        $limit  = $matcherResult->getMaxResults();

        /** @var RemoteAccount[] $remoteAccounts */
        $remoteAccounts = $matcherResult->getList();
        foreach ($remoteAccounts as $remoteAccount) {
            $this->prepareRemoteAccount($remoteAccount);
        }
        $view = $this->view($remoteAccounts, $statusCode, $headers);
        $this->setContentRangeHeader($view, $offset, $limit, $matcherResult->getTotal());

        return $this->handleView($view);
    }

    /**
     * @Operation(
     *     tags={"Получить account (Get account by id)"},
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
     * @return RemoteAccount|null|object
     */
    public function getAction($id)
    {
        $remoteAccount = $this->getRemoteAccount($id);
        $this->prepareRemoteAccount($remoteAccount);
        return $remoteAccount;
    }

    /**
     * @param $id
     *
     * @return RemoteAccount
     */
    private function getRemoteAccount($id)
    {
        $remoteAccount = $this->getRepository(RemoteAccount::class)->findOneBy(['remoteId' => (integer)$id]);
        if (!$remoteAccount) {
            throw $this->createNotFoundException('client not found');
        }

        return $remoteAccount;
    }

    /**
     * @Operation(
     *     tags={"Частично обновить данные account (PATH account)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="uuid1c",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
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

        $remoteAccount = $this->getRemoteAccount($id);

        $requestFields = $request->request->all();

        /** @var InputAccount $input */
        $input = $this->requestMap(InputAccount::class, $requestFields);

        $accountUpdated = $this->patchClass($remoteAccount, $input, $requestFields);

        $violations = $this->validator->validate($accountUpdated);

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
                        'field'   => $key,
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
        $em->persist($accountUpdated);
        $em->flush();

        $this->prepareRemoteAccount($accountUpdated);

        return $accountUpdated;
    }

    /**
     * @param RemoteAccount $remoteAccount
     * @param InputAccount  $input
     *
     * @param array         $requestFields
     *
     * @return RemoteAccount
     * @internal param array $fields
     *
     * @internal param array $isset
     *
     * @internal param array $fields
     */
    private function patchClass(RemoteAccount $remoteAccount, InputAccount $input, array $requestFields)
    {
        if (array_key_exists('uuid1c', $requestFields)) {
            $remoteAccount->setUuid1c($input->getUuid1c());
        }

        return $remoteAccount;
    }

    /**
     * @param InputAccount $input
     *
     * @return array
     */
    private function searchMap(InputAccount $input)
    {
        $fields = [];

        if ($input->getUuid1c() !== null) {
            $fields['uuid1c'] = $input->getUuid1c();
        }

        return $fields;
    }

}
