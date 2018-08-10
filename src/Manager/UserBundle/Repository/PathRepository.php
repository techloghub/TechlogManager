<?php

namespace Manager\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author wukai
 */
class PathRepository extends EntityRepository
{
    public function getList($start, $limit, $params = array())
    {
        $start =(int) $start;
        $limit =(int) $limit;
        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;

        $em = $this->getEntityManager();

        $DQL_TOTAL = "SELECT COUNT(b) FROM ManagerUserBundle:Path b";
        $DQL = "SELECT b FROM ManagerUserBundle:Path b";

        $WHERE = array();

        if (isset($params['startTime'])) {
            $WHERE[] = "b.updateTime>=:startTime";
        }
        if (isset($params['endTime'])) {
            $WHERE[] = "b.updateTime<=:endTime";
        }
        if (isset($params['firstMenu'])) {
            $WHERE[] = "b.firstMenu=:firstMenu";
        }
        if (isset($params['secondMenu'])) {
            $WHERE[] = "b.secondMenu=:secondMenu";
        }
        if (isset($params['operator'])) {
            $WHERE[] = "b.operator=:operator";
        }
        if (isset($params['name'])) {
            $params['name'] = "%{$params['name']}%";
            $WHERE[] = "b.name like :name";
        }

        $WHERE  = $WHERE ? " WHERE " . implode(" AND ", $WHERE) : '';
        $WHERE .= " ORDER BY b.createTime DESC";

        $query_total = $em->createQuery($DQL_TOTAL . $WHERE);
        $query = $em->createQuery($DQL . $WHERE);

        $query_total->setParameters($params);
        $query->setParameters($params);

        $total =(int) $query_total->getSingleScalarResult();
        $first =($start - 1) * $limit;
        $query->setFirstResult($first); // 设置起始元素
        $query->setMaxResults($limit);

        return array ($total, $query->getResult());
    }

    public function getMenu()
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT p.id, p.name, p.menu_1 AS firstMenu, p.menu_2 AS secondMenu, p.route FROM manager_path p";

        $result = $conn->fetchAll($SQL);

        return $result;
    }

    public function findRoute($route, $secondMenu)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT id FROM manager_path 
                WHERE menu_2='" . $secondMenu . "' 
                AND route='" . $route . "' 
                LIMIT 0,1";

        $result = $conn->fetchAssoc($SQL);

        return $result;
    }
}
