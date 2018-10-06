<?php
namespace Manager\TechlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Manager\TechlogBundle\Entity\TaskList;

/**
 * @Route("/tasklist")
 */
class TaskListController extends Controller
{
    private $input_list = array('id', 'name');
    private $range_list = array('insert_time', 'finish_time', 'start_time');
	private $select_list = array(
		'status' => array(0 => '未开始', 1 => '进行中', 2 => '已完成', 3 => '已取消'),
		'priority' => array(4 => '立即去做', 3 => '非常紧急', 2 => '非常重要',
			1 => '比较重要', 0 => '随时都行'),
		'category' => array(0 => '技术', 1 => '工作', 2 => '生活', 3 => '读书')
	);
    private $key_value_map = array(
		'id'			=> array('name'=>'id', 'width'=>2),
		'name'			=> array('name'=>'名称', 'width'=>8),
		'category'		=> array('name'=>'分类', 'width'=>2),
		'priority'		=> array('name'=>'优先级', 'width'=>2),
		'insert_time'	=> array('name'=>'创建时间', 'width'=>5),
		'start_time'	=> array('name'=>'开始时间', 'width'=>5),
		#'update_time'	=> array('name'=>'更新时间', 'width'=>5),
		'finish_time'	=> array('name'=>'完成时间', 'width'=>5),
		'status'		=> array('name'=>'状态', 'width'=>2),
		'remark'		=> array('name'=>'备注', 'width'=>8),
    );

    /**
     * @Route("/list", name="techlog_manager_tasklist_list");
	 * @Template("ManagerTechlogBundle:TaskList:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="techlog_manager_tasklist_query")
	 * @Template("ManagerTechlogBundle:TaskList:query_result.html.twig")
     */
    public function queryAction(Request $request)
    {
        return $this->getQueryParams($request);
    }

    /**
     * @Route("/modify", name="techlog_manager_tasklist_modify");
	 * @Template("ManagerTechlogBundle:TaskList:modify.html.twig")
     */
    public function modifyAction (Request $request)
	{
        $id = $request->get('id');

		if (!empty($id)) {
			$em = $this->getDoctrine()->getEntityManager();
			$entity = $em->getRepository('ManagerTechlogBundle:TaskList')->findOneById($id);
			if (empty($entity))
				throw new \Exception('id is wrong');
		} else {
			$entity = new TaskList();
			$entity->setStatus(0);
		}

		return array(
			'data'=>$entity,
			'select_list'=>$this->select_list
		);
	}

    /**
     * @Route("/modifybasic", name="techlog_manager_tasklist_modifybasic");
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

			$entity = $em->getRepository('ManagerTechlogBundle:TaskList')->findOneById($id);
			if (empty($entity)) {
				return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));
			}

			if ($status > $entity->getStatus()) {
				$entity->setStatus($status);
				if ($status === 1) {
					$entity->setStartTime($date);
				}
				if ($status >= 2) {
					$entity->setFinishTime($date);
				}
			} else if ($entity->getStatus() > $status) {
				return new JsonResponse(array('code'=>1, 'msg'=>'status is wrong'));
			} else {
				$entity->setName($request->get('name'));
				$entity->setRemark($request->get('remark'));
				$entity->setPriority($request->get('priority'));
				$entity->setCategory($request->get('category'));
			}
		} else {
			$entity = new TaskList();
			$entity->setInsertTime($date);
			$entity->setStatus(0);
			$entity->setFinishTime('0000-00-00 00:00:00');
			$entity->setStartTime('0000-00-00 00:00:00');
			$entity->setName($request->get('name'));
			$entity->setRemark($request->get('remark'));
			$entity->setPriority($request->get('priority'));
			$entity->setCategory($request->get('category'));
		}

		$entity->setUpdateTime($date);
		$em->persist($entity);
		$em->flush();

		return new JsonResponse(array('code'=>0, 'msg'=>'更新成功',
			'url'=>$this->generateUrl('techlog_manager_tasklist_list').'?id='.$id));
	}

    private function getQueryParams($request)
    {
        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);

		if ($this->getUser()->getUserName() != 'admin')
			$params['root'] = 0;
		else
			$params['root'] = 1;
		
        if (!isset($params['sortby']))
            $params['sortby'] = 'status';
        if (!isset($params['asc']))
            $params['asc'] = '1';

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
		list($total, $data) = $em->getRepository('ManagerTechlogBundle:TaskList')->getList($start, $limit, $params, $repository_key);
		if (!empty($data)) {
			for ($i = 0; $i < sizeof($data); ++$i) {
				$entity = $data[$i];
				if ($entity['start_time'] === '0000-00-00 00:00:00') {
					$entity['start_time'] = '';
					$data[$i] = $entity;
				}
				if ($entity['finish_time'] === '0000-00-00 00:00:00') {
					$entity['finish_time'] = '';
					$data[$i] = $entity;
				}
			}
		}

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
