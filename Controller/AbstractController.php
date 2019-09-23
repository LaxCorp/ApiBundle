<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Helper\DoctrineMatcherResult;
use LaxCorp\ApiBundle\Services\DoctrineMatcher;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Serializer;


/**
 * Class BaseController
 *
 * @package LaxCorp\ApiBundle\Controller
 */
abstract class AbstractController extends FOSRestController
{

    /**
     * @var DoctrineMatcher 
     */
    public $doctrineMatcher;

    /**
     * @var Serializer
     */
    public $jmsSerializer;

    /**
     * @var KernelInterface
     */
    public $kernel;

    public function __construct(
        DoctrineMatcher $doctrineMatcher,
        Serializer $jmsSerializer,
        KernelInterface $kernel
    )
    {
        $this->doctrineMatcher = $doctrineMatcher;
        $this->jmsSerializer = $jmsSerializer;
        $this->kernel = $kernel;
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
}