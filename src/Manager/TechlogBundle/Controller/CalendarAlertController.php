<?php
namespace Manager\TechlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Manager\TechlogBundle\Entity\TaskList;

/**
 * @Route("/tasklist")
 */
class CalendarAlertController extends Controller
{
    private $input_list = array('id', 'name');
    private $range_list = array('insert_time', 'start_time', 'end_time', 'alert_time', 'period');
    private $select_list = array(
        'status' => array(0 => '正常执行', 1 => '循环执行', 2 => '停止执行'),
        'lunar' => array(0 => '否', 1 => '是'),
        'category' => array(0 => '生日', 1 => '纪念日', 2 => '任务', 3 => '日常'),
        'cycle_type' => array(0 => '不循环', 1 => '日', 2 => '周', 3 => '月', 4 => '年', 5 => '工作日')
    );
    private $key_value_map = array(
        'id'			=> array('name'=>'id', 'width'=>2),
        'name'			=> array('name'=>'名称', 'width'=>8),
        'status'        => array('name'=>'状态', 'width'=>3),
        'category'      => array('name'=>'类别', 'width'=>5),
        'start_time'    => array('name'=>'开始时间', 'width'=>5),
        'end_time'      => array('name'=>'结束时间', 'width'=>5),
        'lunar'         => array('name'=>'是否农历', 'width'=>2),
        'alert_time'    => array('name'=>'上次提醒', 'width'=>5),
        'period'        => array('name'=>'循环周期', 'width'=>2),
        'cycle_type'    => array('name'=>'循环类型', 'width'=>2),
        'insert_time'   => array('name'=>'插入时间', 'width'=>5),
        'remark'		=> array('name'=>'备注', 'width'=>8),
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
