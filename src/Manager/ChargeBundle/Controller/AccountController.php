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
 * @Route("/account")
 */
class AccountController extends Controller
{
    private $input_list = array('esid', 'name', 'cardno', 'category', 'currency');
    private $range_list = array('money', 'updatetime', 'inserttime');
	private $select_list = array();
	private $key_value_map = array(
		'esid'			=> array('name' => 'ID', 'width' => 8),
		'name'			=> array('name' => '账户名', 'width' => 5),
		'money'			=> array('name' => '余额', 'width' => 5),
		'cardno'		=> array('name' => '卡号', 'width' => 8),
		'orderno'		=> array('name' => '账目数', 'width' => 3),
		'category'		=> array('name' => '类别', 'width' => 5),
		'currency'		=> array('name' => '币种', 'width' => 5),
		'updatetime'	=> array('name' => '更新时间', 'width' => 5),
		'inserttime'	=> array('name' => '插入时间', 'width' => 5),
	);
	private $modify_key = array(
		'esid' => false,
		'orderno' => true,
		'money' => true,
		'cardno' => true,
		'category' => true,
		'currency' => true,
	);

    /**
     * @Route("/list", name="charge_account_list");
	 * @Template("ManagerChargeBundle:Account:list.html.twig")
     */
	public function listAction (Request $request)
	{
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="charge_account_query");
	 * @Template("ManagerChargeBundle:Account:query_result.html.twig")
     */
	public function queryAction (Request $request)
	{
        return $this->getQueryParams($request);
	}


    /**
     * @Route("/modify", name="charge_account_modify");
	 * @Template("ManagerChargeBundle:Account:modify.html.twig")
     */
	public function modifyAction (Request $request)
	{
        $id = $request->get('esid');
        if (empty($id))
            throw new \Exception('esid is missing');

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerChargeBundle:Account')
			->getOneByEsid($id, $this->modify_key);
        if (empty($entity))
            throw new \Exception('esid is wrong');

		return array(
			'labels' => array(
				array(
					'href' => $this->generateUrl('charge_account_list'),
					'text' => '账户管理',
					'current' => false
				),
				array(
					'href' => $this->generateUrl('charge_account_modify').'?esid='.$id,
					'text' => '账户修改',
					'current' => true
				),
			),
			'data'=>$entity,
			'modify_key' => $this->modify_key,
			'key_value_map' => $this->key_value_map,
			'commit_url' => $this->generateUrl('charge_account_modifybasic')
		);
	}

    /**
     * @Route("/modifybasic", name="charge_account_modifybasic");
     */
	public function modifybasicAction (Request $request)
	{
    	\date_default_timezone_set('PRC');

        $id = $request->get('esid');
        if (empty($id))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerChargeBundle:Account')
			->findOneByEsid($id);
        if (empty($entity))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));

		foreach ($this->modify_key as $key => $value) {
			$entity->{'set'.strToUpper($key)}($request->get($key));
		}
		$entity->setUpdatetime(date('Y-m-d H:i:s'));
		$em->persist($entity);
		$em->flush();

		return new JsonResponse(array('code'=>0, 'msg'=>'更新成功',
			'url'=>$this->generateUrl('charge_account_list').'?esid='.$id));
	}

    private function getQueryParams($request)
    {
        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);

        if (!isset($params['sortby']))
            $params['sortby'] = 'updatetime';
        if (!isset($params['asc']))
            $params['asc'] = '0';

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
		list($total, $data, $totalmoney) = $em->getRepository('ManagerChargeBundle:Account')->getList($start, $limit, $params, $repository_key);

        $totalPages = (int)(($total + $limit - 1) / $limit);

		return array (
			'title' => '账户管理',
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
			'query_url' => $this->generateUrl('charge_account_query'),
			'totalmoney' => $totalmoney,
			'operate' => array(
				array(
					'href' => $this->generateUrl('charge_account_modify').'?esid=',
					'text' => '修改'
				),
				array(
					'href' => $this->generateUrl('charge_ledgers_list').'?fromAcc=',
					'text' => '明细'
				)
			)
		);
	}

    private function get_repository_key()
    {
        $repository_key = array();
		$repository_key['equal_list'] = $this->input_list;
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
        foreach($params as $count=>$key)
        {
            $value = $request->get($key);
            if($value != NULL)
                $param[$key] = $value;
        }
        return $param;
    }
}
