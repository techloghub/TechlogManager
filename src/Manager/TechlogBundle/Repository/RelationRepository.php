<?php

namespace Manager\TechlogBundle\Repository;

use Component\Util\ParamsUtil;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

use Manager\TechlogBundle\Entity\ArticleTagRelation;

class RelationRepository extends EntityRepository
{
    public function getList($start, $limit, $params, $params_key)
    {
        $start = (int)$start;
        $limit = (int)$limit;

        $start = $start<=0 ? 1 : $start;
        $limit = $limit<=0 ? 20 : $limit;
        $start = ($start-1)*$limit;

        $conn = $this->getConn();

		$space = array('article.title', 'article_tag_relation.*', 'tags.tag_name');

		$total_sql = 'select count(*) as total from ('
			.' select id from tags, article, article_tag_relation'
			.' where tags.tag_id = article_tag_relation.tag_id'
			.' and article.article_id = article_tag_relation.article_id';

		$data_sql = 'select '.implode(', ', $space)
			.' from tags, article, article_tag_relation'
			.' where tags.tag_id = article_tag_relation.tag_id'
			.' and article.article_id = article_tag_relation.article_id';

        list($where_sql, $pkv) = $this->get_where_sql($params, $params_key);

		$total_sql .= $where_sql.') as Temp';

        if (isset($params['sortby']))
        {
            $where_sql .= ' order by '.$params['sortby'];
            if (!isset($params['asc']) || $params['asc'] != 1)
                $where_sql .= ' desc';
		}

        $data_sql .= $where_sql;
        $data_sql .= ' limit '.$start.', '.$limit;

        $total = $conn->fetchAssoc($total_sql, $pkv);
        $data = $conn->fetchAll($data_sql, $pkv);

        return array($total['total'], $data);
    }

    protected function getConn()
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        return $conn;
    }

    protected function get_where_sql ($params, $params_key)
    {
        $where_sql = '';

        $ret_params = array();
        if (is_array($params_key['like_list']))
        {
            foreach ($params_key['like_list'] as $key)
            {
				$sql_key = $key;
                if (isset($params[$key]))
                {
					$where_sql .= " and $key like :$key ";
                    $ret_params[$key] = '%'.$params[$key].'%';
                }
            }
        }

        if (is_array($params_key['equal_list']))
        {
            foreach ($params_key['equal_list'] as $key)
            {
				$sql_key = 'article_tag_relation.'.$key;
                if (isset($params[$key]))
                {
                    $where_sql .= " and $sql_key = :$key ";
                    $ret_params[$key] = $params[$key];
                }
            }
        }

        if (is_array($params_key['range_list']))
        {
            foreach ($params_key['range_list'] as $key)
            {
				$sql_key = 'article_tag_relation.'.$key;
                if (isset($params['start_'.$key]))
                {
                    $where_sql .= " and $sql_key >= :start_$key ";
                    $ret_params['start_'.$key] = $params['start_'.$key];
                }
                if (isset($params['end_'.$key]))
                {
                    $where_sql .= " and $sql_key <= :end_$key ";
                    $ret_params['end_'.$key] = $params['end_'.$key];
                }
            }
        }

        return array($where_sql, $ret_params);
	}
}
?>
