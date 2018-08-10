<?php

namespace Manager\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Component\Util\ParamsUtil;

class RoleRepository extends EntityRepository
{
    public function getList($start, $limit)
    {
        $start = (int)$start;
        $limit = (int)$limit;
        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;

        $em = $this->getEntityManager();

        $DQL_TOTAL = "SELECT COUNT(a) FROM ManagerUserBundle:Role a";
        $DQL = "SELECT a FROM ManagerUserBundle:Role a ORDER BY a.createTime DESC";

        $query_total = $em->createQuery($DQL_TOTAL);
        $query = $em->createQuery($DQL);

        $total =(int)$query_total->getSingleScalarResult();
        $first = ($start - 1) * $limit;
        $query->setFirstResult($first); // 设置起始元素
        $query->setMaxResults($limit);

        return array($total, $query->getResult());
    }

    public function query($start, $limit, $params = array())
    {
        $start = (int)$start;
        $limit = (int)$limit;
        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;
        $start = ($start - 1) * $limit;

        $WHERE = array();
        if (isset($params['secondMenu'])) {
            $WHERE[] = "a.route_id='" . $params['secondMenu'] . "'";
        } else if (isset($params['firstMenu'])) {
            $WHERE[] = "a.route_id='" . $params['firstMenu'] . "'";
        }
        if (isset($params['operator'])) {
            $WHERE[] = "r.operator='" . $params['operator'] . "'";
        }
        if (isset($params['name'])) {
            $WHERE[] = "r.name like '%{$params['name']}%'";
        }

        $WHERE  = $WHERE ? " WHERE " . implode(" AND ", $WHERE) : '';
        $WHERE .= " ORDER BY r.create_time DESC";
        $LIMIT  = " LIMIT {$start}, {$limit}";

        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL_TOTAL = "SELECT COUNT(DISTINCT r.id) AS total FROM manager_role r
                LEFT JOIN manager_access a
                ON r.id = a.role_id
        ";

        $SQL = "SELECT DISTINCT r.* FROM manager_role r
                LEFT JOIN manager_access a
                ON r.id = a.role_id
        ";

        $result_total = $conn->fetchAssoc($SQL_TOTAL . $WHERE);
        $result = $conn->fetchAll($SQL . $WHERE . $LIMIT);
        $conn->close();

        $route = array();
        foreach ($result as $key => $value) {
            $route[$key]['id']   = $value['id'];
            $route[$key]['name'] = $value['name'];
            $route[$key]['operator'] = $value['operator'];
            $route[$key]['updateTime'] = $value['update_time'];
            $route[$key]['createTime'] = $value['create_time'];
        }

        $total = $result_total['total'];

        return array($total, $route);
    }

    public function getRoleList()
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT id, name AS roleName FROM manager_role";

        $result = $conn->fetchAll($SQL);

        return $result;
    }

    public function findRoleByRoleId($roleId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT r.*, a.route_id FROM manager_role r
                LEFT JOIN manager_access a
                ON r.id = a.role_id
                WHERE r.id='" . $roleId . "'
        ";

        $result = $conn->fetchAll($SQL);
        $conn->close();

        $route = array();
        foreach ($result as $value) {
            $route['id']   = $value['id'];
            $route['name'] = $value['name'];
            $route['operator'] = $value['operator'];
            $route['updateTime'] = $value['update_time'];
            $route['createTime'] = $value['create_time'];
            $route['route'][]  = $value['route_id'];
        }

        return $route;
    }

    public function findRoleByRoleName($roleName)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT r.id, a.route_id FROM manager_role r
                LEFT JOIN manager_access a
                ON r.id = a.role_id
                WHERE r.name='" . $roleName . "'
        ";
        $result = $conn->fetchAll($SQL);
        $conn->close();

        return $result;
    }

    public function addRole($data)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $conn->insert('manager_role', $data);
        $role_id = $conn->lastInsertId();
        $conn->close();

        return $role_id;
    }

    public function updateRole($data, $identifier)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $conn->update('manager_role', $data, $identifier);
        $role_id = $conn->lastInsertId();
        $conn->close();

        return $role_id;
    }

    public function addAccess($data)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");
        $conn->insert('manager_access', $data);
        $conn->close();
    }

    public function deleteAccess($data)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");
        $conn->delete('manager_access', $data);
        $conn->close();
    }

    public function findRoleAccessPath($roleIds)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT route_id FROM manager_access WHERE role_id IN(" . implode(',', $roleIds) . ")";
        $result = $conn->fetchAll($SQL);
        $conn->close();

        return $result;
    }

	public function findRoleWithUser($userParams, $roleParams)
	{
		$em = $this->getEntityManager();
		$dql = "SELECT role FROM ManagerUserBundle:Role role, ManagerUserBundle:User user WHERE role.id=user.roleId";
		$where .= " and ".ParamsUtil::getSqlWhereStr(array_keys($userParams), $userParams, 'user');
		$where .= " and ".ParamsUtil::getSqlWhereStr(array_keys($roleParams), $roleParams, 'role');
		$entities = $em->createQuery($dql.$where)->getResult();
		return $entities;
	}
}
