<?php


namespace Manager\ApiBundle\Controller;

use Component\Library\LunarHelper;
use Exception;
use Manager\TechlogBundle\Entity\CalendarAlert;
use Manager\TechlogBundle\ManagerTechlogBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/calendar")
 */
class CalendarController extends Controller
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

        $id = $request->get('calendar_id');
        $date = date('Y-m-d H:i:s');

        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ManagerTechlogBundle:CalendarAlert')->findOneById($id);
        if (empty($entity)) {
            return new JsonResponse(array('code' => 1, 'msg' => 'id is wrong'));
        }

        $entity->setAlertTime($date);
        $next_time = LunarHelper::getNextAlert($entity);
        if ($next_time == '1970-01-01 08:00:00' || $entity->getStatus() == 0) {
            $entity->setStatus(2);
        }
        $entity->setNextTime($next_time);
        $em->persist($entity);
        $em->flush();
        return  new JsonResponse(array('code' => 1, 'msg' => '更新成功'));
    }
}
