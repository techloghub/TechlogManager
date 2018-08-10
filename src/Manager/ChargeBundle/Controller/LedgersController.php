<?php
namespace Manager\ChargeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Component\Library\HttpCurl;
use Manager\ChargeBundle\Repository\ElasticRepository;

/**
 * @Route("/ledgers")
 */
class LedgersController extends Controller
{
    private $input_list = array('esid', 'currency', 'comment');
    private $range_list = array('money', 'date', 'inserttime');
	private $select_list = array();
	private $key_value_map = array(
		'esid'			=> array('name' => 'ID', 'width' => 8),
		'money'			=> array('name' => '金额', 'width' => 3),
		'currency'		=> array('name' => '币种', 'width' => 3),
		'tag'			=> array('name' => '交易方式', 'width' => 3),
		'date'			=> array('name' => '交易时间', 'width' => 5),
		'fromAcc'		=> array('name' => '账户', 'width' => 5),
		'toAcc'			=> array('name' => '目标账户', 'width' => 5),
		'category'		=> array('name' => '分类', 'width' => 3),
		'subcategory'	=> array('name' => '细分分类', 'width' => 3),
		'comment'		=> array('name' => '备注', 'width' => 8),
		'inserttime'	=> array('name' => '插入时间', 'width' => 5),
	);
	private $modify_key = array(
		'esid' => false,
		'money' => true,
		'date' => true,
		'currency' => true,
		'fromAcc' => true,
		'toAcc' => true,
		'category' => true,
		'subcategory' => true,
		'comment' => true,
	);

    /**
     * @Route("/list", name="charge_ledgers_list");
	 * @Template("ManagerChargeBundle:Account:list.html.twig")
     */
	public function listAction (Request $request)
	{
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="charge_ledgers_query");
	 * @Template("ManagerChargeBundle:Account:query_result.html.twig")
     */
	public function queryAction (Request $request)
	{
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/modify", name="charge_ledgers_modify");
	 * @Template("ManagerChargeBundle:Account:modify.html.twig")
     */
	public function modifyAction (Request $request)
	{
        $id = $request->get('esid');
        if (empty($id))
            throw new \Exception('esid is missing');

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerChargeBundle:Ledgers')
			->getOneByEsid($id, $this->modify_key);
        if (empty($entity))
            throw new \Exception('esid is wrong');

		return array(
			'labels' => array(
				array(
					'href' => $this->generateUrl('charge_ledgers_list'),
					'text' => '账单管理',
					'current' => false
				),
				array(
					'href' => $this->generateUrl('charge_ledgers_modify').'?esid='.$id,
					'text' => '账单修改',
					'current' => true
				),
			),
			'data' => $entity,
			'modify_key' => $this->modify_key,
			'key_value_map' => $this->key_value_map,
			'commit_url' => $this->generateUrl('charge_ledgers_modifybasic')
		);
	}

    /**
     * @Route("/modifybasic", name="charge_ledgers_modifybasic");
     */
	public function modifybasicAction (Request $request)
	{
    	\date_default_timezone_set('PRC');
		return new JsonResponse(array('code'=>0, 'msg'=>'暂不允许修改',
			'url'=>$this->generateUrl('charge_ledgers_list').'?esid='.$id));
	}

    private function getQueryParams($request)
    {
        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $em = $this->getDoctrine()->getEntityManager();

		$this->select_list = $em->getRepository('ManagerChargeBundle:Ledgers')
			->getSelectList();

        $repository_key = $this->get_repository_key();

        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);

        if (!isset($params['sortby']))
            $params['sortby'] = 'date';
        if (!isset($params['asc']))
            $params['asc'] = '0';


		list($total, $data) = $em->getRepository('ManagerChargeBundle:Ledgers')
			->getList($start, $limit, $params, $repository_key, $this->key_value_map);

        $totalPages = (int)(($total + $limit - 1) / $limit);

		return array (
			'title' => '账单管理',
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
			'query_url' => $this->generateUrl('charge_ledgers_query'),
			'operate' => array(
				array(
					'href' => $this->generateUrl('charge_ledgers_modify').'?esid=',
					'text' => '修改'
				)
			)
		);
	}

    private function get_repository_key()
    {
        $repository_key = array();
		$repository_key['like_list'] = array('comment');
		$repository_key['equal_list'] = 
			array_merge(array_diff($this->input_list, array('comment')),
				array_keys($this->select_list));
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
		$params_key = array_merge($params_key, array_keys($this->select_list),
			array('asc', 'sortby', 'limit'));
        return $params_key;
    }

    private function getParams($request, $params)
    {
		$param = array();
        foreach($params as $count => $key)
        {
            $value = $request->get($key);
            if($value != NULL)
                $param[$key] = $value;
        }
        return $param;
    }
}
