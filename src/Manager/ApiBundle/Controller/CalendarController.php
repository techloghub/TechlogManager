<?php


namespace Manager\ApiBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/calendar")
 */
class CalendarController
{
    /**
     * @Route("/setalerttime");
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function setAlertTimeAction(Request $request)
    {
        \date_default_timezone_set('PRC');

        $id = $request->get('id');
        return null;
    }
}