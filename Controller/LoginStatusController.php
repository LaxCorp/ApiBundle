<?php

namespace LaxCorp\ApiBundle\Controller;

use LaxCorp\ApiBundle\Form\LoginStatusType;
use LaxCorp\ApiBundle\Model\LoginStatus;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @inheritdoc
 * @Security("has_role('ROLE_API_SUPPORT') or has_role('ROLE_SUPER_ADMIN')")
 */
class LoginStatusController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Login status"},
     *     summary="Get customer tarification and expired date",
     *     @SWG\Parameter(
     *         name="login",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="LaxCorp\ApiBundle\Model\LoginStatus"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when the user is not authorized to say hello"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when the login is not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     *
     * @Rest\View()
     * @Rest\Route(path="login_status")
     *
     * @param Request $request
     *
     * @return object
     */
    public function cgetAction(Request $request)
    {
        $conflicts = [];
        $invalid   = [];

        $customerHelper = $this->get('billing_partner.helper.customer_helper');
        $eventHelper    = $this->get('billing_partner.helper.event_helper');

        /** @var LoginStatus $input */
        $input  = $this->requestMap(LoginStatus::class, $request->query->all());
        $output = $input;

        $violations = $this->get('validator')->validate($input);

        if ($violations->count() != 0) {
            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $key   = $violation->getPropertyPath();
                $value = $violation->getInvalidValue();

                $invalid[] = [
                    'field'   => $key,
                    'value'   => $value,
                    'message' => $violation->getMessage()
                ];
            }

            return $this->errorView($conflicts, $invalid);
        }

        $customer = $customerHelper->getCustomerByLogin($input->getLogin());

        if (!$customer) {

            return $this->view(null, 404);
        }

        /** @var \DateTime $toDate */
        $toDate = $customer->getToDate();

        $output->setExpiredDate($toDate);

        /** @var \DateTime $tarificationDate */
        $tarificationDate = null;

        $tariffs = $customer->getCustomerTariffs();

        foreach ($tariffs as $tariff) {
            if ($tariff->getActive() && $tariff->getTarificationDate() > $tarificationDate) {
                $tarificationDate = $tariff->getTarificationDate();
            }
        }

        $output->setTarificationDate($tarificationDate);

        $account = $customer->getAccount();

        $event = $eventHelper->getPrognoseLimitReached($account->getId());

        if ($event) {
            /** @var \DateTime $prognoseTo */
            $prognoseTo = $event->getPrognoseTo();
            $output->setAccountProblemDate($prognoseTo);

            $needSum = $event->getSum();
            $output->setAccountNeedSum($needSum);
        }

        return $output;
    }

}