<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Form\CreateReportErrorType;
use LaxCorp\ApiBundle\Model\InputCustomerRequest;
use App\Entity\CustomerRequest;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Rest\RouteResource("CustomerRequest", pluralize=false)
 * @Security("has_role('ROLE_API_SUPPORT') or has_role('ROLE_SUPER_ADMIN')")
 */
class CustomerRequestController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Сообщить об ошибке (сообщение с рабочих мест)"},
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
     *         name="created_at",
     *         in="body",
     *         description="ISO 8601 - 2017-10-20T11:27:25+07:00 or range (>=2017-09-26T18:28:07+07:00,<=2017-10-20T11:27:25+07:00)",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="customer_login",
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
     *         name="description",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="job_email",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful|not found",
     *         @SWG\Schema(ref=@Model(type="App\Entity\CustomerRequest"))
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
     * @REST\Route(path="report_error")
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

        $repository = $this->getRepository(CustomerRequest::class);

        /** @var InputCustomerRequest $input */
        $input  = $this->requestMap(InputCustomerRequest::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"Сообщить об ошибке (сообщение с рабочих мест)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="created_at",
     *         in="body",
     *         description="ISO 8601 - 2017-10-20T11:27:25+07:00 or range (>=2017-09-26T18:28:07+07:00,<=2017-10-20T11:27:25+07:00)",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="customer_login",
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
     *         name="description",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="job_email",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="App\Entity\CustomerRequest"))
     *     )
     * )
     *
     *
     * @Rest\Route(path="report_error/{id}", requirements={ "id": "\d+" })
     * @param $id
     *
     * @return CustomerRequest|null|object
     */
    public function getAction($id)
    {

        $request = $this->getRepository(CustomerRequest::class)->find((integer)$id);

        if (!$request) {
            throw $this->createNotFoundException('not found');
        }

        return $request;
    }


    /**
     * @Operation(
     *     tags={"Сообщить об ошибке (сообщение с рабочих мест)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="customerLogin",
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
     *         name="description",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="jobEmail",
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
     * @REST\Route(path="report_error")
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
     * @inheritdoc
     */
    private function postForm(Request $request)
    {
        $customerRequest = new CustomerRequest();

        $conflicts = [];
        $invalid   = [];

        $form = $this->createForm(CreateReportErrorType::class, $customerRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$customerRequest->getCustomerLogin()) {
                $invalid[] = [
                    'field'   => 'customer_login',
                    'value'   => $customerRequest->getCustomerLogin(),
                    'message' => 'Required: customer_login'
                ];
            }

            if ($invalid) {
                return $this->errorView($conflicts, $invalid);
            }

            $violations = $this->validator->validate($customerRequest);

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

            $customerHelper = $this->get('billing_partner.helper.customer_helper');

            $customer = $customerHelper->getCustomerByLogin($customerRequest->getCustomerLogin());

            if (!$customer) {
                $invalid[] = [
                    'field'   => 'customer_login',
                    'value'   => $customerRequest->getCustomerLogin(),
                    'message' => 'login does not exist in billing'
                ];

                return $this->errorView($conflicts, $invalid);
            }

            $accountId     = $customer->getAccount()->getId();
            $customerLogin = $customer->getLogin();
            $customerState = $customer->getState();
            $customerEmail = $customer->getEmail();

            $clientHelper = $this->get('app.client_helper');
            $client       = $clientHelper->getClientByAccountId($accountId);

            $userName  = '-нет личного кабинета-';

            $jobEmail = $customerRequest->getJobEmail();

            $userEmail = ($jobEmail) ? $jobEmail : $customerEmail;

            if ($client) {
                $customerRequest->setClient($client);

                $user = $client->getUser();

                $userName  = $user->getName();
                $userEmail = $user->getEmail();
            }

            $em = $this->getDoctrine()->getManager();

            $requestComponentRepository = $em->getRepository('App:RequestComponent');

            $sectionName = $this->getParameter('jira_customer_request_section_name');

            $component = $requestComponentRepository->findOneBy(['name' => $sectionName]);

            if (!$component) {
                $invalid[] = [
                    'field'   => '',
                    'value'   => $sectionName,
                    'message' => 'jira_component not found'
                ];

                return $this->errorView($conflicts, $invalid);
            }

            $translator = $this->get('translator');

            $description = "*{$translator->trans('Full name')}:* {$userName}\n";
            $description .= "*{$translator->trans('Email')}:* {$userEmail}\n";

            if($jobEmail && $jobEmail!==$userEmail){
                $description .= "*{$translator->trans('label.job_email')}:* {$jobEmail}\n";
            }

            $description .= "*Рабочее место:*\n";

            $description .= "_{$translator->trans('Login')}_: {$customerLogin} ";
            $description .= "_{$translator->trans('State')}_: {$customerState}\n";

            $description .= "\n{$customerRequest->getDescription()}";

            $jiraApi = $this->get('app.jira_api');

            $issue  = $jiraApi->createIssue($customerRequest->getName(), $description, $component->getJiraComponentId());
            $status = $jiraApi->getDefaultStatus();

            if (!$issue || !$status) {

                $invalid[] = [
                    'field'   => '',
                    'value'   => 'issue=' . $issue . ', status:' . $status,
                    'message' => 'jira error!'
                ];

                return $this->errorView($conflicts, $invalid);
            }

            $customerRequest->setComponent($component->getName());
            $customerRequest->setJiraIssueId($issue['id']);
            $customerRequest->setJiraStatusId($status['id']);

            $em->persist($customerRequest);
            $em->flush();

            $fileInfo = $customerRequest->getFileInfo();

            if ($fileInfo) {

                $uploadDir  = $this->getParameter('app_request_files_upload_dir');
                $filePath   = realpath($uploadDir . $fileInfo['fileName']);
                $attachment = $jiraApi->createIssueAttachment($issue['id'], $fileInfo['fileName'], $filePath);

                if ($attachment) {

                    $description .= "\n\n" . $jiraApi->getAttachedFileString($attachment['filename']);
                    $jiraApi->updateIssue($issue['id'], $description);
                }

            }

            $view = $this->view($customerRequest, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        return $form;
    }

    /**
     * @param InputCustomerRequest $input
     *
     * @return array
     */
    private function searchMap(InputCustomerRequest $input)
    {
        $fields = [];

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getCustomerLogin() !== null) {
            $fields['customerLogin'] = $input->getCustomerLogin();
        }

        if ($input->getName() !== null) {
            $fields['name'] = $input->getName();
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

        if (isset($_order['created_at'])) {
            $order['createdAt'] = $_order['created'];
        }

        if (isset($_order['customer_login'])) {
            $order['customerLogin'] = $_order['customer_login'];
        }

        return $order;
    }

    /**
     * @param $param
     *
     * @return CustomerRequest
     */
    private function findOneBy($param)
    {
        return $this->getRepository(CustomerRequest::class)->findOneBy($param);
    }
}