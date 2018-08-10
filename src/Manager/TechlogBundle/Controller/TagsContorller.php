<?php
namespace Manager\TechlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Manager\TechlogBundle\Entity\Tags;

/**
 * @Route("/tags")
 */
class TagsContorller extends Controller
{
    private $input_list = array('tag_id', 'tag_name');
    private $range_list = array('total', 'inserttime');
	private $select_list = array();
	private $key_value_map = array(
		'tag_id'	=> array('name'=>'id', 'width'=>3),
		'tag_name'	=> array('name'=>'名称', 'width'=>12),
		'total'		=> array('name'=>'文章数', 'width'=>3),
		'inserttime'	=> array('name'=>'创建时间', 'width'=>6),
	);

    /**
     * @Route("/list", name="techlog_manager_tags_list");
	 * @Template("ManagerTechlogBundle:Tags:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/query", name="techlog_manager_tags_query");
	 * @Template("ManagerTechlogBundle:Tags:query_result.html.twig")
     */
    public function queryAction (Request $request)
    {
        return $this->getQueryParams($request);
	}

    /**
     * @Route("/modify", name="techlog_manager_tags_modify");
	 * @Template("ManagerTechlogBundle:Tags:modify.html.twig")
     */
    public function modifyAction (Request $request)
    {
        if (!$request->get('tag_id'))
            throw new \Exception('id is missing');
        $id = $request->get('tag_id');

        $em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('ManagerTechlogBundle:Tags')->findOneByTagId($id);
        if (empty($entity))
            throw new \Exception('id is wrong');

		return array('data'=>$entity);
	}

    /**
     * @Route("/modifybasic", name="techlog_manager_tags_modifybasic");
     */
    public function modifybasicAction (Request $request)
    {
    	\date_default_timezone_set('PRC');
        if (!$request->get('tag_id'))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is missing'));
        $id = $request->get('tag_id');

        $em = $this->getDoctrine()->getEntityManager();
		$rp = $em->getRepository('ManagerTechlogBundle:Tags');
		$entity = $rp->findOneByTagId($id);
        if (empty($entity))
			return new JsonResponse(array('code'=>1, 'msg'=>'id is wrong'));

		$tag_name = $request->get('tag_name');
		if (empty($tag_name))
			return new JsonResponse(array('code'=>1, 'msg'=>'tag_name cannot be empty'));

		$new_entity = $rp->findOneByTagName($tag_name);
		if (empty($new_entity))
		{
			$entity->setTagName($tag_name);
			$entity->setInserttime(date('Y-m-d H:i:s'));
			$em->persist($entity);
			$em->flush();

			return new JsonResponse(array('code'=>0, 'msg'=>'更新成功', 'url'=>$this->generateUrl('techlog_manager_tags_list').'?tag_id='.$id));
		}
		else
		{
			$new_id = $new_entity->getTagId();
			$rp->setTagId($id, $new_id);

			return new JsonResponse(array('code'=>0, 'msg'=>'更新成功', 'url'=>$this->generateUrl('techlog_manager_tags_list').'?tag_id='.$new_id));}
	}

    private function getQueryParams($request)
    {
        $params_key = $this->get_keys();
        $params = $this->getParams($request, $params_key);

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
		list($total, $data) = $em->getRepository('ManagerTechlogBundle:Tags')->getList($start, $limit, $params, $repository_key);

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
        $repository_key['like_list'] = array_diff($this->input_list, array('tag_id'));
        $repository_key['equal_list'] = array_merge(array_keys($this->select_list), array('tag_id'));
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
