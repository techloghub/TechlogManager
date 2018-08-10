<?php

namespace Manager\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getList($start, $limit, $queryParams = array())
    {
        $start =(int) $start;
        $limit =(int) $limit;
        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;
        $start = ($start - 1) * $limit;

        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL_TOTAL = "SELECT COUNT(*) AS total FROM manager_user u";

        $SQL = "SELECT u.id, u.username, u.role_id AS roleId, u.operator, u.update_time AS updateTime, u.create_time AS createTime, r.name AS roleName
                FROM manager_user u 
                LEFT JOIN manager_role r 
                ON u.role_id = r.id 
        ";

        $LIMIT = '';
        $WHERE = array();
        if (isset($queryParams['operator'])) {
            $WHERE[] = "u.operator='" . $queryParams['operator'] . "'";
        }
        if (isset($queryParams['startTime'])) {
            $WHERE[] = "u.update_time>='" . $queryParams['startTime'] . "'";
        }
        if (isset($queryParams['endTime'])) {
            $WHERE[] = "u.update_time<='" . $queryParams['endTime'] . "'";
        }
        if (isset($queryParams['username'])) {
            $WHERE[] = "u.username='" . $queryParams['username'] . "'";
        }
        if (isset($queryParams['roleId'])) {
            $WHERE[] = "u.role_id='" . $queryParams['roleId'] . "'";
        }

        $LIMIT = " LIMIT " . $start . ',' . $limit;

        $WHERE  = $WHERE ? " WHERE " . implode(" AND ", $WHERE) : '';
        $WHERE .= " ORDER BY u.create_time DESC ";

        $result_total = $conn->fetchAssoc($SQL_TOTAL . $WHERE);
        $result = $conn->fetchAll($SQL . $WHERE . $LIMIT);

        return array($result_total['total'], $result);
    }

    public function findUserRole($username)
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeQuery("SET NAMES utf8");

        $SQL = "SELECT role_id FROM manager_user WHERE username='" . $username . "'";

        $result = $conn->fetchAll($SQL);
        $conn->close();

        return $result;
    }

    public function getUserByName($username)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->executeQuery("SET NAMES utf8");

        $DQL    = "SELECT u FROM ManagerUserBundle:User u WHERE u.username=:username";
        $query  = $em->createQuery($DQL);
        $query->setParameter("username", $username);
        $result = $query->getResult();

        return array_shift($result);
    }

    public function getUserList()
    {
        $em = $this->getEntityManager();
        $em->getConnection()->executeQuery("SET NAMES utf8");
        $DQL = "SELECT u FROM ManagerUserBundle:User u";
        $query = $em->createQuery($DQL);
        $result = $query->getResult();

        return $result;
    }

    public function isAvailableUser($username)
    {
        $em = $this->getEntityManager();
        $store = $em->getConnection();
        $sql = "select distinct(username) as username from manager_user";
        $sth = $store->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $v) {
            $users[] = $v['username'];
        }

        if (!in_array($username, $users)) {
            return false;
        } else {
            return true;
        }
    }

    public function isRightPassword($username, $password)
    {
        $em = $this->getEntityManager();
        $store = $em->getConnection();
        $sql = "select password from manager_user_auth where username=:username";
        $sth = $store->prepare($sql);
        $sth->execute(array('username' => $username));
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($result)) {
            return false;
        }

        if ($result['password'] != md5($password)) {
            return false;
        }

        return true;
    }
}
