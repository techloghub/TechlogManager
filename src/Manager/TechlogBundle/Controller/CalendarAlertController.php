<?php
namespace Manager\TechlogBundle\Controller;

use Component\Library\LunarHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Manager\TechlogBundle\Entity\CalendarAlert;

/**
 * @Route("/calendar")
 */
class CalendarAlertController extends Controller
{
    private $input_list = array('id', 'name');
    private $range_list = array('insert_time', 'start_time', 'end_time', 'alert_time', 'period');
    private $select_list = array(
        'status' => array(0 => '单次执行', 1 => '循环执行', 2 => '停止执行'),
        'lunar' => array(0 => '否', 1 => '是'),
        'category' => array(0 => '生日', 1 => '纪念日', 2 => '任务', 3 => '日常', 4 => '节日'),
        'cycle_type' => array(0 => '不循环', 1 => '日', 2 => '周', 3 => '月', 4 => '年', 5 => '工作日')
    );
    private $key_value_map = array(
        'id'			=> array('name'=>'id', 'width'=>1),
        'name'			=> array('name'=>'名称', 'width'=>8),
        'status'        => array('name'=>'状态', 'width'=>3),
        'category'      => array('name'=>'类别', 'width'=>1),
        'start_time'    => array('name'=>'开始时间', 'width'=>5),
        'end_time'      => array('name'=>'结束时间', 'width'=>5),
        'lunar'         => array('name'=>'农历', 'width'=>1),
        'alert_time'    => array('name'=>'上次提醒', 'width'=>5),
        'next_time'     => array('name'=>'下次提醒', 'width'=>5),
        'period'        => array('name'=>'周期', 'width'=>1),
        'cycle_type'    => array('name'=>'单位', 'width'=>2),
        'insert_time'   => array('name'=>'插入时间', 'width'=>5),
        'remark'		=> array('name'=>'备注', 'width'=>13),
    );

    /**
     * @Route("/list", name="task_manager_calendar_list");
     * @Template("ManagerTechlogBundle:CalendarAlert:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->getQueryParams($request);
    }

    /**
     * @Route("/query", name="task_manager_calendar_query")
     * @Template("ManagerTechlogBundle:CalendarAlert:query_result.html.twig")
     */
    public function queryAction(Request $request)
    {
        return $this->getQueryParams($request);
    }

    /**
     * @Route("/modify", name="task_manager_calendar_modify");
     * @Template("ManagerTechlogBundle:CalendarAlert:modify.html.twig")
     * @throws \Exception
     */
    public function modifyAction (Request $request)
	{
        $id = $request->get('id');
        $lunar = '';

		if (!empty($id)) {
			$em = $this->getDoctrine()->getEntityManager();
			$entity = $em->getRepository('ManagerTechlogBundle:CalendarAlert')->findOneById($id);
			if (empty($entity))
				throw new \Exception('id is wrong');
            $lunar = LunarHelper::getSorlarDate($entity->getStartTime());
            $lunar = substr($lunar, 0, strpos($lunar, ' '));
		} else {
			$entity = new CalendarAlert();
		}

		return array(
			'data' => $entity,
			'lunar' => $lunar,
			'select_list' => $this->select_list
		);
	}

    /**
     * @Route("/modifybasic", name="task_manager_calendar_modifybasic");
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function modifybasicAction (Request $request)
	{
			\date_default_timezone_set('PRC');

			$id = $request->get('id');
			$date = date('Y-m-d H:i:s');
			$em = $this->getDoctrine()->getEntityManager();
			if (!empty($id)) {
				$status = $request->get('status');
				if (!in_array($status, range(0, 3))) {
					return new JsonResponse(array('code'=>1, 'msg'=>'状态值错误'));
				}

				$entity = $em->getRepository('ManagerTechlogBundle:CalendarAlert')->findOneById($id);
				if (empty($entity)) {
					return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));
				}
			} else {
				$entity = new CalendarAlert();
				$entity->setInsertTime($date);
				$entity->setAlertTime('1970-01-01 08:00:00');
			}

			$entity->setName($request->get('name'));
			$entity->setCategory($request->get('category'));
			$entity->setStatus($request->get('status'));
			$entity->setStartTime($request->get('start_time'));
			$endTime = $request->get('end_time');
			if (empty($endTime)) {
				$entity->setEndTime($request->get('start_time'));
			} else {
				$entity->setEndTime($endTime);
			}
			$entity->setLunar($request->get('lunar'));
            $entity->setPeriod($request->get('period', 0));
			$entity->setCycleType($request->get('cycle_type'));
			$entity->setRemark($request->get('remark'));
			$entity->setUpdateTime($date);
            $entity->setNextTime(LunarHelper::getNextAlert($entity));
			$em->persist($entity);
			$em->flush();

			return new JsonResponse(array('code' => 0, 'msg'=>'设置成功',
				'url'=>$this->generateUrl('task_manager_calendar_list').'?id='.$id));
	}

    private function getQueryParams($request)
    {
        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);

        if ($this->getUser()->getUserName() != 'admin')
            $params['root'] = 0;
        else
            $params['root'] = 1;

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
        list($total, $data) = $em->getRepository('ManagerTechlogBundle:CalendarAlert')->getList($start, $limit, $params, $repository_key);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        return array (
            'sortby' => $params['sortby'],
            'asc' => $params['asc'],
            'data'  => $data,
            'total' => $total,
            'start' => $start,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'select_list' => $this->select_list,
            'input_list' => $this->input_list,
            'key_value_map' => $this->key_value_map,
            'range_list' => $this->range_list,
            'params' => $params,
            'params_key' => $params_key,
        );
    }

    private function get_repository_key()
    {
        $repository_key = array();
        $repository_key['like_list'] = array_diff($this->input_list, array('id'));
        $repository_key['equal_list'] = array_merge(array_keys($this->select_list), array('id'));
        $repository_key['range_list'] = $this->range_list;

        return $repository_key;
    }

    private function get_keys()
    {
        $params_key = $this->input_list;
        foreach ($this->range_list as $value)
        {
            $params_key[] = 'start_'.$value;
            $params_key[] = 'end_'.$value;
        }
        $params_key = array_merge($params_key, array_keys($this->select_list), array('asc', 'sortby'));
        return $params_key;
    }

    private function getParams($request, $params)
    {
        $param = array();
        foreach($params as $count=>$key)
        {
            $value = $request->get($key);
            if($value != NULL)
                $param[$key] = $value;
        }
        return $param;
    }
}
