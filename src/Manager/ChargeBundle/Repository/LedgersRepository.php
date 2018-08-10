<?php

namespace Manager\ChargeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Manager\ChargeBundle\Entity\Ledgers;
use Manager\ChargeBundle\Entity\Account;

class LedgersRepository extends EntityRepository
{
    public function getList($start, $limit, $params, $params_key, $key_value_map)
    {
        $start = (int)$start;
        $limit = (int)$limit;

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 20 : $limit;
        $start = ($start-1)*$limit;

        $conn = $this->getConn();

        $total_sql = 'select count(*) as total from ledgers where 1';
		$data_sql = 'select '.implode(', ', array_keys($key_value_map)).
			' from ledgers where 1';
        list($where_sql, $pkv) = $this->get_where_sql($params, $params_key);

        if (isset($params['sortby']))
        {
            $where_sql .= ' order by '.$params['sortby'];
            if (!isset($params['asc']) || $params['asc'] != 1)
                $where_sql .= ' desc';
		}

        $total_sql .= $where_sql;
        $data_sql .= $where_sql;
        $data_sql .= ' limit '.$start.', '.$limit;

        $total = $conn->fetchAssoc($total_sql, $pkv);
        $data = $conn->fetchAll($data_sql, $pkv);

		$account_sql = 'select esid, name from account';
        $account_list = $conn->fetchAll($account_sql);
		$account_kv = array();
		foreach ($account_list as $account) {
			$account_kv[$account['esid']] = $account['name'];
		}

		for ($i = 0; $i < count($data); ++$i) {
			if (isset($account_kv[$data[$i]['fromAcc']])) {
				$data[$i]['fromAcc'] = $account_kv[$data[$i]['fromAcc']];
			}
			if (isset($account_kv[$data[$i]['toAcc']])) {
				$data[$i]['toAcc'] = $account_kv[$data[$i]['toAcc']];
			}
		}

        return array($total['total'], $data);
    }

	public function getOneByEsid($id, $modify_key) {
		$data_sql = 'select '.implode(', ', array_keys($modify_key)).
			' from ledgers where esid = :esid';
        $conn = $this->getConn();
        $data = $conn->fetchAll($data_sql, array('esid' => $id));
		return empty($data[0]) ? null : $data[0];
	}

	public function getSelectList() {
		$ret = array();
		$category_sql = 'select category from ledgers group by category';
        $conn = $this->getConn();
        $data = $conn->fetchAll($category_sql);
		foreach ($data as $category) {
			$ret['category'][$category['category']] = $category['category'];
		}
		$subcategory_sql = 'select subcategory from ledgers group by subcategory';
        $conn = $this->getConn();
        $data = $conn->fetchAll($subcategory_sql);
		foreach ($data as $category) {
			if (!empty($category['subcategory'])) {
				$ret['subcategory'][$category['subcategory']] = $category['subcategory'];
			}
		}
		$account_sql = 'select esid, name from account';
        $account_list = $conn->fetchAll($account_sql);
		$account_kv = array();
		foreach ($account_list as $account) {
			$ret['fromAcc'][$account['esid']] = $account['name'];
			$ret['toAcc'][$account['esid']] = $account['name'];
		}
		return $ret;
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
