<?php

namespace Manager\TechlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

use Manager\TechlogBundle\Entity\TaskList;

class CalendarAlertRepository extends EntityRepository
{
    public function getList($start, $limit, $params, $params_key)
    {
        $start = (int)$start;
        $limit = (int)$limit;

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 20 : $limit;
        $start = ($start-1)*$limit;

        $conn = $this->getConn();

		$space = array('id', 'name', 'insert_time', 'update_time', 'start_time',
			'end_time', 'alert_time', 'status', 'lunar', 'cycle_type', 'period',
			'category', 'remark', 'next_time');

        $total_sql = 'select count(*) as total from calendar_alert where 1';
        $data_sql = 'select '.implode(', ', $space).' from calendar_alert where 1';
        list($where_sql, $pkv) = $this->get_where_sql($params, $params_key);

        if (isset($params['sortby'])) {
			$where_sql .= ' order by '.$params['sortby'];
			if (!isset($params['asc']) || $params['asc'] != 1)
				$where_sql .= ' desc';
			$where_sql .= ', id desc';
		} else {
			$where_sql .= ' order by next_time desc';
		}

        $total_sql .= $where_sql;
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
                if (isset($params[$key]))
                {
                    $where_sql .= " and $key = :$key ";
                    $ret_params[$key] = $params[$key];
                }
            }
        }

        if (is_array($params_key['range_list']))
        {
            foreach ($params_key['range_list'] as $key)
            {
                if (isset($params['start_'.$key]))
                {
                    $where_sql .= " and $key >= :start_$key ";
                    $ret_params['start_'.$key] = $params['start_'.$key];
                }
                if (isset($params['end_'.$key]))
                {
                    $where_sql .= " and $key <= :end_$key ";
                    $ret_params['end_'.$key] = $params['end_'.$key];
                }
            }
        }

        return array($where_sql, $ret_params);
	}
}
?>
