<?php
namespace Manager\TechlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Manager\TechlogBundle\Entity\Article;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    private $input_list = array('article_id', 'title');
    private $range_list = array('access_count', 'inserttime', 'updatetime');
	private $select_list = array(
		'category_id' => array(1=>'龙潭书斋', 2=>'读书笔记', 3=>'龙渊阁记',
			4=>'技术分享', 5=>'龙泉日记', 6=>'龙泉财报'),
		'online' => array(0 => '下线', 1 => '上线'),
	);
    private $key_value_map = array(
		'article_id'	=> array('name'=>'id', 'width'=>3),
		'online'		=> array('name'=>'上线', 'width'=>2),
		'title'			=> array('name'=>'标题', 'width'=>12),
		'category_id'	=> array('name'=>'分类', 'width'=>4),
		'access_count'	=> array('name'=>'访问数', 'width'=>3),
		'comment_count'	=> array('name'=>'评论数', 'width'=>3),
		'title_desc'	=> array('name'=>'备注', 'width'=>5),
		'updatetime'	=> array('name'=>'更新时间', 'width'=>5),
		'inserttime'	=> array('name'=>'插入时间', 'width'=>5),
    );

    /**
     * @Route("/list", name="techlog_manager_article_list");
	 * @Template("ManagerTechlogBundle:Article:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="techlog_manager_article_query")
	 * @Template("ManagerTechlogBundle:Article:query_result.html.twig")
     */
    public function queryAction(Request $request)
    {
        return $this->getQueryParams($request);
    }

    /**
     * @Route("/modify", name="techlog_manager_article_modify");
	 * @Template("ManagerTechlogBundle:Article:modify.html.twig")
     */
    public function modifyAction (Request $request)
	{
        if (!$request->get('article_id'))
            throw new \Exception('id is missing');
        $id = $request->get('article_id');

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerTechlogBundle:Article')->findOneByArticleId($id);
        if (empty($entity))
            throw new \Exception('id is wrong');

		return array(
			'data'=>$entity,
			'select_list'=>$this->select_list
		);
	}

    /**
     * @Route("/modifybasic", name="techlog_manager_article_modifybasic");
     */
    public function modifybasicAction (Request $request)
	{
    	\date_default_timezone_set('PRC');

		$id = $request->get('article_id');
		if (empty($id))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is missing'));

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerTechlogBundle:Article')->findOneByArticleId($id);
        if (empty($entity))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));

		$entity->setTitle($request->get('title'));
		$entity->setOnline($request->get('online'));
		$entity->setCategoryId($request->get('category_id'));
		$entity->setTitleDesc($request->get('title_desc'));
		$entity->setUpdatetime(date('Y-m-d H:i:s'));
		$em->persist($entity);
		$em->flush();

		return new JsonResponse(array('code'=>0, 'msg'=>'更新成功',
			'url'=>$this->generateUrl('techlog_manager_article_list').'?article_id='.$id));
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
            $params['sortby'] = 'article_id';
        if (!isset($params['asc']))
            $params['asc'] = '1';

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
		list($total, $data) = $em->getRepository('ManagerTechlogBundle:Article')->getList($start, $limit, $params, $repository_key);

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
        $repository_key['like_list'] = array_diff($this->input_list, array('article_id'));
        $repository_key['equal_list'] = array_merge(array_keys($this->select_list), array('article_id'));
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
?>
