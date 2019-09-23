<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Model\InputPatchEvents;
use LaxCorp\ApiBundle\Model\InputEvents;
use App\Entity\Events1c;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Rest\RouteResource("Events", pluralize=false)
 */
class EventsController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"События (events)"},
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
     *         description="Default: _order[created]=DESC",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="created",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="updated",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="processing",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Parameter(
     *         name="completed",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Parameter(
     *         name="resource_name",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="resource_id",
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
     *
     * @Rest\QueryParam(name="_limit",  requirements="\d+", default=2, nullable=true, strict=true)
     * @Rest\QueryParam(name="_offset", requirements="\d+", default=0, nullable=true, strict=true)
     * @Rest\QueryParam(name="_order", nullable=true, description="Default: _order[created]=DESC")
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

        $repository = $this->getRepository(Events1c::class);

        /** @var InputEvents $input */
        $input  = $this->requestMap(InputEvents::class, $request->query->all());
        $fields = $this->searchMap($input);

        $matcherResult = $this->getMatcher()->matching($repository, $fields, $order, $_offset, $_limit);

        return $this->createViewByMatcher($matcherResult, 200);
    }

    /**
     * @Operation(
     *     tags={"События (events)"},
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
     * @return Response|Events1c
     */
    public function getAction($id)
    {
        $result = $this->findOneBy(['id' => (integer)$id]);
        if (!$result) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * @Operation(
     *     tags={"События (events)"},
     *     summary="",
     *     @SWG\Parameter(
     *         name="processing",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Parameter(
     *         name="completed",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="boolean")
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

        $events1c = $this->findOneBy(['id' => (integer)$id]);

        if (!$events1c) {
            throw new NotFoundHttpException();
        }

        $requestFields = $request->request->all();

        /** @var InputPatchEvents $input */
        $input = $this->requestMap(InputPatchEvents::class, $requestFields);

        $eventUpdated = $this->patchClass($events1c, $input, $requestFields);

        $violations = $this->get('validator')->validate($eventUpdated);

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

        $em->persist($eventUpdated);
        $em->flush();

        return $eventUpdated;
    }


    /**
     * @param Events1c         $events1c
     * @param InputPatchEvents $input
     *
     * @param array            $requestFields
     *
     * @return Events1c
     */
    private function patchClass(Events1c $events1c, InputPatchEvents $input, array $requestFields)
    {
        $processing = $input->getProcessing();
        if (array_key_exists('processing', $requestFields)) {
            $events1c->setProcessing($processing);
        }

        $completed = $input->getCompleted();
        if (array_key_exists('completed', $requestFields)) {
            $events1c->setCompleted($completed);
        }

        return $events1c;
    }


    /**
     * @param $param
     *
     * @return Events1c
     */
    private function findOneBy($param)
    {
        return $this->getRepository(Events1c::class)->findOneBy($param);
    }

    /**
     * @param InputEvents $input
     *
     * @return array
     */
    private function searchMap(InputEvents $input)
    {
        $fields = [];

        if ($input->getId() !== null) {
            $fields['id'] = $input->getId();
        }

        if ($input->getCreatedAt() !== null) {
            $fields['createdAt'] = $input->getCreatedAt();
        }

        if ($input->getUpdatedAt() !== null) {
            $fields['updatedAt'] = $input->getUpdatedAt();
        }

        if ($input->getProcessing() !== null) {
            $fields['processing'] = $input->getProcessing();
        }

        if ($input->getCompleted() !== null) {
            $fields['completed'] = $input->getCompleted();
        }

        if ($input->getResourceName() !== null) {
            $fields['resourceName'] = $input->getResourceName();
        }

        if ($input->getResourceId() !== null) {
            $fields['resourceId'] = $input->getResourceId();
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
            $_order['createdAt'] = 'DESC';
        }

        $order = [];

        if (isset($_order['id'])) {
            $order['id'] = $_order['id'];
        }

        if (isset($_order['created'])) {
            $order['createdAt'] = $_order['created'];
        }

        if (isset($_order['updated'])) {
            $order['updatedAt'] = $_order['updated'];
        }

        if (isset($_order['processing'])) {
            $order['processing'] = $_order['processing'];
        }

        if (isset($_order['completed'])) {
            $order['completed'] = $_order['completed'];
        }

        if (isset($_order['resource_name'])) {
            $order['resourceName'] = $_order['resource_name'];
        }

        if (isset($_order['resource_id'])) {
            $order['resourceId'] = $_order['resource_id'];
        }

        return $order;
    }

}