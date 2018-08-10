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
use Manager\TechlogBundle\Entity\ArticleTagRelation;

/**
 * @Route("/relation")
 */
class RelationController extends Controller
{
	private $input_list		= array(
		'id', 'article_id', 'tag_id', 'title', 'tag_name'
	);
    private $range_list		= array('inserttime');
	private $select_list	= array();
	private $key_value_map	= array(
		'id'			=> array('name' => 'id',		'width'=>3),
		'title'			=> array('name' => '标题',		'width'=>12),
		'tag_name'		=> array('name' => '标签',		'width'=>12),
		'inserttime'	=> array('name' => '插入时间',	'width'=>5),
		'article_id'	=> array('name' => '文章 ID'),
		'tag_id'		=> array('name' => '标签 ID'),
	);

    /**
     * @Route("/list", name="techlog_manager_relation_list");
	 * @Template("ManagerTechlogBundle:Relation:list.html.twig")
     */
    public function listAction (Request $request)
    {
        return $this->get_query_params($request);
	}

    /**
     * @Route("/query", name="techlog_manager_relation_query")
	 * @Template("ManagerTechlogBundle:Relation:query_result.html.twig")
     */
    public function queryAction(Request $request)
    {
        return $this->get_query_params($request);
    }

    /**
     * @Route("/delete", name="techlog_manager_relation_del")
     */
    public function deleteAction(Request $request)
    {
		try
		{
			if (empty($request->get('article_id')
				or empty($request->get('tag_id')))
			)
				return new JsonResponse(array('code'=>1, 'msg'=>'参数错误'));

			$tag_id = $request->get('tag_id');
			$article_id = $request->get('article_id');

			$em = $this->getDoctrine()->getEntityManager();

			$count = $em->createQuery(
				'SELECT count(relation) as total'
				.' FROM ManagerTechlogBundle:ArticleTagRelation relation'
				.' WHERE relation.articleId = :article_id'
				.' AND relation.tagId = :tag_id'
			)->setParameter('article_id', $article_id)
			->setParameter('tag_id', $tag_id)
			->getOneOrNullResult();
			if ($count['total'] < 1)
			{
				return new JsonResponse(
					array('code'=>1, 'msg'=>'该项不存在')
				);
			}
			else if ($count['total'] == 1)
			{
				$category_id = $em->createQuery(
					'SELECT article.categoryId'
					.' FROM ManagerTechlogBundle:Article article'
					.' WHERE article.articleId = :article_id'
				)->setParameter('article_id', $article_id)->getOneOrNullResult();
				if (!in_array($category_id['categoryId'], array(2, 5, 6)))
				{
					return new JsonResponse(
						array(
							'code'	=> 1,
							'msg'	=> '无法删除文章的最后一个标签'."\n"
							.'请先为文章添加至少一个标签'
						)
					);
				}
			}

			$rp = $em->getRepository('ManagerTechlogBundle:ArticleTagRelation');
			$entity = $rp->findOneBy(
				array(
					'tagId'		=> $tag_id,
					'articleId' => $article_id,
				)
			);
			if (empty($entity))
			{
				return new JsonResponse(
					array(
						'code'	=> 1,
						'msg'	=> '找不到对应记录'
					)
				);
			}

			$em->remove($entity);
			$em->flush();

			$count = $em->createQuery(
				'SELECT count(relation) as total'
				.' FROM ManagerTechlogBundle:ArticleTagRelation relation'
				.' WHERE relation.tagId = :tag_id'
			)->setParameter('tag_id', $tag_id)->getOneOrNullResult();
			if ($count['total'] == 0)
			{
				$tag_entity = $em->getRepository('ManagerTechlogBundle:Tags')
					->findOneByTagId($tag_id);
				if (empty($tag_entity))
				{
					return new JsonResponse(
						array(
							'code'	=> 1,
							'msg'	=> 'tag not found'
						)
					);
				}
				$em->remove($tag_entity);
				$em->flush();
			}

			return new JsonResponse(
				array(
					'code'	=> 0,
					'url'	=> $this->generateUrl('techlog_manager_relation_list')
						.'?article_id='.$article_id,
					'msg'	=> '删除成功'
				)
			);
		} catch (\Exception $e)
		{
			return new JsonResponse(array('code'=>1, 'msg'=>$e->getMessage()));
		}
    }

    /**
     * @Route("/add", name="techlog_manager_relation_add");
	 * @Template("ManagerTechlogBundle:Relation:modify.html.twig")
     */
    public function addAction (Request $request)
    {
		if (!$request->get('article_id') || !$request->get('title'))
            throw new \Exception('parameter is missing');

		return array(
			'article_id'	=> $request->get('article_id'),
			'title'			=> $request->get('title')
		);
	}

	/**
     * @Route("/addbasic", name="techlog_manager_relation_addbasic");
	 */
	public function addbasicAction (Request $request)
	{
		try
		{
			if (
				!$request->get('article_id')
				|| !$request->get('tag')
			)
			{
				return new JsonResponse(
					array(
						'code'	=> 1,
						'msg'	=> 'parameter is missing'
					)
				);
			}

			$article_id = $request->get('article_id');
			$tag = $request->get('tag');

			$em = $this->getDoctrine()->getEntityManager();
			$tag_entity = $em->getRepository('ManagerTechlogBundle:Tags')
				->findOneByTagName($tag);
			if (empty($tag_entity))
			{
				$tag_entity = new Tags();
				$tag_entity->setTagName($tag);
				$tag_entity->setInserttime(date('Y-m-d H:i:s'));
				$em->persist($tag_entity);
				$em->flush();
			}
			$tag_id = $tag_entity->getTagId();

			$relation_entity = $em
				->getRepository('ManagerTechlogBundle:ArticleTagRelation')
				->findOneBy(
					array(
						'articleId'	=> $article_id,
						'tagId'		=> $tag_id
					)
				);
			if (!empty($relation_entity))
			{
				return new JsonResponse(
					array(
						'code'	=> 0,
						'msg'	=> '该标签已存在',
						'url'	=> $this->generateUrl('techlog_manager_relation_list')
							.'?article_id='.$article_id.'&tag_id='.$tag_id
					)
				);
			}

			$relation_entity = new ArticleTagRelation();
			$relation_entity->setArticleId($article_id);
			$relation_entity->setTagId($tag_id);
			$relation_entity->setInserttime(date('Y-m-d H:i:s'));
			$em->persist($relation_entity);
			$em->flush();

			return new JsonResponse(
				array(
					'code'	=> 0,
					'msg'	=> '添加成功',
					'url'	=> $this->generateUrl('techlog_manager_relation_list')
						.'?article_id='.$article_id.'&tag_id='.$tag_id
				)
			);
		} catch (\Exception $e)
		{
			return new JsonResponse(array('code'=>1, 'msg'=>$e->getMessage()));
		}
	}

    private function get_query_params($request)
    {
        $params_key = $this->get_keys();
        $params = $this->get_params($request, $params_key);

		if ($this->getUser()->getUserName() != 'admin')
			$params['root'] = 0;
		else
			$params['root'] = 1;
		
        if (!isset($params['sortby']))
            $params['sortby'] = 'id';
        if (!isset($params['asc']))
            $params['asc'] = '1';

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = ($limit <= 0 or $limit > 1000) ? 20 : $limit;

        $repository_key = $this->get_repository_key();

        $em = $this->getDoctrine()->getEntityManager();
		list($total, $data) =
			$em->getRepository('ManagerTechlogBundle:ArticleTagRelation')
				->getList($start, $limit, $params, $repository_key);

        $totalPages = (int)(($total + $limit - 1) / $limit);

		return array (
            'asc'		=> $params['asc'],
            'data'		=> $data,
            'total'		=> $total,
            'start'		=> $start,
            'limit'		=> $limit,
            'params'	=> $params,
            'sortby'	=> $params['sortby'],
            'params_key'	=> $params_key,
            'input_list'	=> $this->input_list,
            'range_list'	=> $this->range_list,
            'totalPages'	=> $totalPages,
            'select_list'	=> $this->select_list,
            'key_value_map'	=> $this->key_value_map,
		);
	}

    private function get_repository_key()
    {
        $repository_key = array();
		$repository_key['like_list']	= array_diff(
			$this->input_list,
			array('article_id', 'id', 'tag_id')
		);
		$repository_key['equal_list']	= array_merge(
			array_keys($this->select_list),
			array('article_id', 'id', 'tag_id')
		);
        $repository_key['range_list']	= $this->range_list;

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
		$params_key = array_merge(
			$params_key,
			array_keys($this->select_list),
			array('asc', 'sortby')
		);
        return $params_key;
    }

    private function get_params($request, $params)
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
