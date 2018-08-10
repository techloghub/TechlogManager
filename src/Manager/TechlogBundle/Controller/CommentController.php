<?php
namespace Manager\TechlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Manager\TechlogBundle\Entity\Comment;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    private $input_list = array('comment_id', 'article_id', 'nickname', 'qq', 'email', 'ip');
    private $range_list = array('updatetime', 'inserttime');
	private $select_list = array('online' => array(0=>'下线', 1=>'上线'));
    private $key_value_map = array(
		'comment_id'	=> array('name'=>'id', 'width'=>3),
		'article_id'	=> array('name'=>'文章id', 'width'=>3),
		'floor'			=> array('name'=>'楼层', 'width'=>3),
		'nickname'		=> array('name'=>'昵称', 'width'=>8),
		'qq'			=> array('name'=>'QQ 号码', 'width'=>5),
		'email'			=> array('name'=>'Email', 'width'=>8),
		'ip'			=> array('name'=>'IP 地址', 'width'=>8),
		'reply'			=> array('name'=>'回复楼层', 'width'=>3),
		'online'		=> array('name'=>'状态', 'width'=>3),
		'updatetime'	=> array('name'=>'更新时间', 'width'=>5),
		'inserttime'	=> array('name'=>'插入时间', 'width'=>8),
    );

    /**
     * @Route("/list", name="techlog_manager_comment_list");
	 * @Template("ManagerTechlogBundle:Comment:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="techlog_manager_comment_query")
	 * @Template("ManagerTechlogBundle:Comment:query_result.html.twig")
     */
    public function queryAction(Request $request)
    {
        return $this->getQueryParams($request);
    }

    /**
     * @Route("/modify", name="techlog_manager_comment_modify");
	 * @Template("ManagerTechlogBundle:Comment:modify.html.twig")
     */
    public function modifyAction (Request $request)
	{
        if (!$request->get('comment_id'))
            throw new \Exception('id is missing');
        $id = $request->get('comment_id');

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerTechlogBundle:Comment')->findOneByCommentId($id);
        if (empty($entity))
            throw new \Exception('id is wrong');
		$article = $em->getRepository('ManagerTechlogBundle:Article')->findOneByArticleId($entity->getArticleId());

		return array(
			'data'=>$entity,
			'select_list'=>$this->select_list,
			'article' => $article
		);
	}

    /**
     * @Route("/modifybasic", name="techlog_manager_comment_modifybasic");
     */
    public function modifybasicAction (Request $request)
	{
    	\date_default_timezone_set('PRC');

		$id = $request->get('comment_id');
		if (empty($id))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is missing'));

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerTechlogBundle:Comment')->findOneByCommentId($id);
        if (empty($entity))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));

		$entity->setNickname($request->get('nickname'));
		$entity->setEmail($request->get('email'));
		$entity->setQq($request->get('qq'));
		$entity->setReply($request->get('reply'));
		$entity->setContent($request->get('content'));
		$entity->setOnline($request->get('online'));
		$entity->setUpdatetime(date('Y-m-d H:i:s'));
		$em->persist($entity);
		$em->flush();

		return new JsonResponse(array('code'=>0, 'msg'=>'更新成功',
			'url'=>$this->generateUrl('techlog_manager_comment_list').'?comment_id='.$id));
	}

    private function getQueryParams($request)
    {
        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);
		
        if (!isset($params['sortby']))
            $params['sortby'] = 'inserttime';
        if (!isset($params['asc']))
            $params['asc'] = '0';

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
		list($total, $data) = $em->getRepository('ManagerTechlogBundle:Comment')->getList($start, $limit, $params, $repository_key);

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
        $repository_key['like_list'] = array_diff($this->input_list, array('comment_id'));
        $repository_key['equal_list'] = array_merge(array_keys($this->select_list), array('comment_id'));
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
