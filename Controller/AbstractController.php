<?php

namespace LaxCorp\ApiBundle\Controller;

use App\Entity\RemoteAccount;
use LaxCorp\ApiBundle\Helper\DoctrineMatcherResult;
use LaxCorp\ApiBundle\Services\DoctrineMatcher;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\SerializationContext;
use LaxCorp\BillingPartnerBundle\Helper\CustomerHelper;
use LaxCorp\BillingPartnerBundle\Helper\MappingHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use LaxCorp\BillingPartnerBundle\Helper\VersionHelper as BillingVersionHelper;
use LaxCorp\BillingPartnerBundle\Helper\AccountOperationHelper;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\EventListener\CounteragentUpdateSubscriber;
use App\Helper\ClientHelper;
use App\Services\Jira\JiraApi;

/**
 * Class BaseController
 *
 * @package LaxCorp\ApiBundle\Controller
 */
abstract class AbstractController extends AbstractFOSRestController
{

    /**
     * @var TranslatorInterface
     */
    public $translator;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var DoctrineMatcher
     */
    public $doctrineMatcher;

    /**
     * @var SerializerInterface
     */
    public $jmsSerializer;

    /**
     * @var KernelInterface
     */
    public $kernel;

    /**
     * @var BillingVersionHelper
     */
    public $billingVersionHelper;

    /**
     * @var ValidatorInterface
     */
    public $validator;

    /**
     * @var CounteragentUpdateSubscriber
     */
    public $counteragentUpdateSubscriber;

    /**
     * @var AccountOperationHelper
     */
    public $accountOperationHelper;

    /**
     * @var EventDispatcherInterface
     */
    public $dispatcher;

    /**
     * @var LegacyEventDispatcherProxy
     */
    public $legacyDispatcher;

    /**
     * @var CustomerHelper
     */
    public $customerHelper;

    /**
     * @var ClientHelper
     */
    public $clientHelper;

    /**
     * @var JiraApi
     */
    public $jiraApi;

    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        DoctrineMatcher $doctrineMatcher,
        SerializerInterface $jmsSerializer,
        KernelInterface $kernel,
        BillingVersionHelper $billingVersionHelper,
        ValidatorInterface $validator,
        CounteragentUpdateSubscriber $counteragentUpdateSubscriber,
        AccountOperationHelper $accountOperationHelper,
        EventDispatcherInterface $eventDispatcher,
        CustomerHelper $customerHelper,
        ClientHelper $clientHelper,
        JiraApi $jiraApi
    ) {
        $this->translator                   = $translator;
        $this->logger                       = $logger;
        $this->doctrineMatcher              = $doctrineMatcher;
        $this->jmsSerializer                = $jmsSerializer;
        $this->kernel                       = $kernel;
        $this->billingVersionHelper         = $billingVersionHelper;
        $this->validator                    = $validator;
        $this->counteragentUpdateSubscriber = $counteragentUpdateSubscriber;
        $this->accountOperationHelper       = $accountOperationHelper;
        $this->dispatcher                   = $eventDispatcher;
        $this->legacyDispatcher             = LegacyEventDispatcherProxy::decorate($eventDispatcher);
        $this->customerHelper               = $customerHelper;
        $this->clientHelper                 = $clientHelper;
        $this->jiraApi                      = $jiraApi;
    }

    /**
     * @return \LaxCorp\ApiBundle\Services\DoctrineMatcher|object
     */
    public function getMatcher()
    {
        return $this->doctrineMatcher;
    }

    /**
     * @return \JMS\Serializer\Serializer|object
     */
    public function getSerializer()
    {
        return $this->jmsSerializer;
    }

    /**
     * @param View $view
     * @param int  $offset
     * @param int  $limit
     * @param int  $total
     */
    protected function setContentRangeHeader(View $view, $offset, $limit, $total)
    {

        $offset = (int)$offset;
        $limit  = (int)$limit;
        $total  = (int)$total;

        if ($limit > 0 && $total > 0 && $offset < $total) {
            $end = $offset + $limit;
            $end = $end > $total ? $total : $end;
            if ($end < $total) {
                $view->setStatusCode(Response::HTTP_PARTIAL_CONTENT);
                $view->setHeader('Content-Range', "items $offset-$end/$total");
            }
        }
    }

    /**
     * @param Request $request
     * @param string  $paramName
     *
     * @return array
     */
    protected function getScopesByRequest(Request $request, $paramName = '_scope')
    {
        $scopes = array_map('trim', explode(',', $request->get($paramName)));
        $scopes = array_merge($scopes, [GroupsExclusionStrategy::DEFAULT_GROUP]);

        return $scopes;
    }

    /**
     * @param Request $request
     * @param bool    $enableMaxDepthChecks
     *
     * @return SerializationContext
     */
    protected function getSerializationContext(Request $request, $enableMaxDepthChecks = true)
    {
        $scopes = $this->getScopesByRequest($request);

        $serializationContext = SerializationContext::create()
            ->setGroups(array_merge($scopes, [GroupsExclusionStrategy::DEFAULT_GROUP]));

        if ($enableMaxDepthChecks) {
            $serializationContext->enableMaxDepthChecks();
        }

        return $serializationContext;
    }

    /**
     * @param DoctrineMatcherResult $matcherResult
     * @param null                  $statusCode
     * @param array                 $headers
     *
     * @return Response
     */
    protected function createViewByMatcher(
        DoctrineMatcherResult $matcherResult, $statusCode = null, array $headers = []
    ) {
        $offset = $matcherResult->getFirstResult();
        $limit  = $matcherResult->getMaxResults();

        $view = $this->view($matcherResult->getList(), $statusCode, $headers);
        $this->setContentRangeHeader($view, $offset, $limit, $matcherResult->getTotal());

        return $this->handleView($view);
    }

    /**
     * @param string $class
     *
     * @return EntityRepository
     */
    protected function getRepository($class)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository($class);

        if ($repository instanceof EntityRepository) {
            return $repository;
        }

        throw new \RuntimeException('Repository class must be instance of EntityRepository.');
    }


    /**
     * @param $entity
     * @param $arr
     *
     * @return object
     */
    protected function requestMap($entity, $arr)
    {
        $format  = 'json';
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $json = $this->getSerializer()->serialize($arr, $format, $context);

        return $this->getSerializer()->deserialize($json, $entity, $format);
    }

    /**
     * @param array $conflicts
     * @param array $invalid
     *
     * @return Response
     */
    protected function errorView($conflicts = [], $invalid = [])
    {
        if ($conflicts) {
            $code = Response::HTTP_CONFLICT;
            $view = $this->view([
                'error' => [
                    'code'    => $code,
                    'message' => 'Conflict',
                    'fields'  => $conflicts
                ]
            ], $code);
        } else {
            $code = Response::HTTP_BAD_REQUEST;
            $view = $this->view([
                'error' => [
                    'code'    => $code,
                    'message' => 'Bad Request',
                    'fields'  => $invalid
                ]
            ], $code);
        }

        return $this->handleView($view);
    }

    /**
     * @param RemoteAccount $remoteAccount
     */
    protected function prepareRemoteAccount(RemoteAccount $remoteAccount): void{
        $profiles = $remoteAccount->getProfiles();
        foreach ($profiles as $profile){
            $customerId = $profile->getCustomerId();
            $profile->setCustomer($this->customerHelper->getCustomer($customerId));
        }
    }
}
