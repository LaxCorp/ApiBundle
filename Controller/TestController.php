<?php
/**
 * Created by PhpStorm.
 * User: kravchuk
 * Date: 10.04.17
 * Time: 9:27
 */

namespace LaxCorp\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use JMS\Serializer\Serializer;
use LaxCorp\ApiBundle\Services\DoctrineMatcher;
use LaxCorp\BillingPartnerBundle\Helper\VersionHelper as BillingVersionHelper;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;


/**
 * @REST\RouteResource("Test", pluralize=false)
 */
class TestController extends AbstractController
{

    /**
     * @Operation(
     *     tags={"Проверка соединения (test)"},
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
     *         response="500",
     *         description="Server error"
     *     )
     * )
     *
     */
    public function getAction()
    {
        $build_time = new \DateTime();
        $build_time->setTimestamp(filemtime($this->kernel->getProjectDir()));

        $billingVersion = $this->billingVersionHelper->getVersion();

        $version = [
            'current_time' => new \DateTime(),
            'deploy_rev' => $this->getParameter('deploy_rev'),
            'build_time' => $build_time,
            'billing'    => $billingVersion->getIncrementalApiVersion()
        ];

        return $version;
    }
}