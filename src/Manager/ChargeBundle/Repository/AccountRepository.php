<?php

namespace Manager\ChargeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Manager\ChargeBundle\Entity\Account;

class AccountRepository extends EntityRepository
{
    public function getList($start, $limit, $params, $params_key)
    {
        $start = (int)$start;
        $limit = (int)$limit;

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 20 : $limit;
        $start = ($start-1)*$limit;

        $conn = $this->getConn();

		$space = array('esid', 'name', 'cardno', 'category', 'orderno',
			'currency', 'money', 'updatetime', 'inserttime');

        $total_sql = 'select count(*) as total from account where 1';
        $data_sql = 'select '.implode(', ', $space).' from account where 1';
        $totalmoney_sql = 'select format(sum(money), 2) as total from account where 1';
        list($where_sql, $pkv) = $this->get_where_sql($params, $params_key);

        if (isset($params['sortby']))
        {
            $where_sql .= ' order by '.$params['sortby'];
            if (!isset($params['asc']) || $params['asc'] != 1)
                $where_sql .= ' desc';
		}

        $total_sql .= $where_sql;
        $totalmoney_sql .= $where_sql;
        $data_sql .= $where_sql;
        $data_sql .= ' limit '.$start.', '.$limit;

        $total = $conn->fetchAssoc($total_sql, $pkv);
        $data = $conn->fetchAll($data_sql, $pkv);
        $totalmoney = $conn->fetchAll($totalmoney_sql, $pkv);

        return array($total['total'], $data, $totalmoney[0]['total']);
    }

	public function getOneByEsid($id, $modify_key) {
		$data_sql = 'select '.implode(', ', array_keys($modify_key)).
			' from account where esid = :esid';
        $conn = $this->getConn();
        $data = $conn->fetchAll($data_sql, array('esid' => $id));
		return empty($data[0]) ? null : $data[0];
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
