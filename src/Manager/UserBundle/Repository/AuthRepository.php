<?php

namespace Manager\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AuthRepository extends EntityRepository
{
    public function getQuery($page, $pageSize, $params)
    {
        list($total, $users) = $this->getAuditQuery($page, $pageSize, $params);
        return array($total, $users);
    }

    public function getAuditQuery($page, $pageSize, $params)
    {
        $em = $this->getEM();
        $store = $em->getConnection();

        $table = $this->getQueryTables($params);
        $where = $this->getQueryConditions($params);
        $offset = ($page - 1) * $pageSize;
        $limit = "limit $pageSize offset $offset";

        $tableTotal = $table;
        $whereTotal = $where;

        $sqlTotal = "select count(*) as total from $tableTotal $whereTotal";
        $sth = $store->prepare($sqlTotal);
        $sth->execute($params);
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        $total = $result['total'];

        $sql = "select a.* from $table $where $limit";
        $sth = $store->prepare($sql);
        $sth->execute($params);
        $softs = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return array($total, $softs);
    }

    /**
     * 根据搜索条件自动调整需要关联的表
     */
    public function getQueryTables(&$params)
    {
        $tables = "manager_user_auth as a";
        return $tables;
    }

    /**
     * 自动调整搜索条件
     */
    public function getQueryConditions(&$params)
    {
        $conditions = array();
        if (! empty($params['username'])) {
            $conditions[] = "a.username=:username";
        }

        $condition = implode(" and ", $conditions);
        if (empty($condition)) {
            return "";
        } else {
            return "where $condition";
        }
    }

    private function getEM()
    {
        return $this->getEntityManager();
    }
}
