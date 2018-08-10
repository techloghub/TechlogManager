<?php

namespace Manager\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Manager\UserBundle\Entity\Auth;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/auth")
 */
class AuthController extends Controller
{
    public $defaultPage = 1;
    public $defaultPageSize = 20;

    /**
     * @Route("/list", name="user_auth_list")
     * @Template("ManagerUserBundle:Auth:list.html.twig")
     */
    public function listAction()
    {
        $request = $this->getRequest();

        $em = $this->getEM();
        $page = $this->defaultPage;
        $pageSize = $this->defaultPageSize; //limit
        $rp = $em->getRepository('ManagerUserBundle:Auth');

        list($total, $users) = $rp->getQuery($page, $pageSize, array());
        $totalPages = ceil($total / $pageSize);

        return array(
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $users,
        );
    }

    /**
     * @Route("/query", name="user_auth_query")
     * @Template("ManagerUserBundle:Auth:query_result.html.twig")
     */
    public function queryAction()
    {
        $request = $this->getRequest();

        $params = $this->getQueryParams($request);
        $page = ($request->get("page") < 1) ? $this->defaultPage : $request->get("page");
        $pageSize = ($request->get("page_size") < 1) ? $this->defaultPageSize : $request->get("page_size");

        $em = $this->getEM();
        list($total, $users) = $em->getRepository('ManagerUserBundle:Auth')
            ->getQuery($page, $pageSize, $params);
        $totalPages = ceil($total / $pageSize);

        return array(
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $users,
        );
    }

    /**
     * @Route("/new", name="user_auth_new")
     * @Template("ManagerUserBundle:Auth:new.html.twig")
     */
    public function newAction()
    {
        return array();
    }

    /**
     * @Route("/add", name="user_auth_add")
     */
    public function addAction(Request $request)
    {
        $username = trim($request->get('username'));
        $password = $request->get('password');
        $email    = $request->get('email');

        if (stripos($username, " ") !== false) {
            throw new \Exception('用户名包含空格，不合法');
        }

        if (empty($username) || empty($password) || empty($email)) {
            throw new \Exception('信息填写不完全');
        }

        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneByUsername($username);
        if (!empty($entity))
        {
            throw new \Exception('用户名已添加');
        }

        $entity = new Auth();
        $entity->setUsername($username);
        $entity->setPassword(md5($password));
        $entity->setEmail($email);
        $entity->setCreateTime(new \DateTime());
        $entity->setUpdateTime(new \DateTime());
        $entity->setOperator($this->getUser()->getUserName());
        $em->persist($entity);
        $em->flush();

        $response = $this->redirect($this->generateUrl('user_auth_list'));
        return $response;
    }

    /**
     * @Route("/change", name="user_auth_change")
     * @Template("ManagerUserBundle:Auth:change.html.twig")
     */
    public function changeAction(Request $request)
    {
        $aid = $request->get('a_id');
        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneById($aid);
        if (empty($entity)) {
            throw new \Exception('没有此用户');
        }

        $params = array('entity'=>$entity);
        $user_name = $this->getUser()->getUserName();
        if ($user_name != $entity->getUsername())
            $params['not_user'] = 1;

        return $params;
    }

    /**
     * @Route("/modify", name="user_auth_modify")
     */
    public function modifyAction(Request $request)
    {
        $aid      = $request->get('a_id');
        $password = $request->get('password');
        $email    = $request->get('email');

        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneById($aid);
        if (empty($entity)) {
            throw new \Exception('没有此用户');
        }

        if (!empty($password)) {
            $entity->setPassword(md5($password));
        }
        $entity->setEmail($email);
        $em->persist($entity);
        $em->flush();

        $response = $this->redirect($this->generateUrl('user_auth_list'));
        return $response;
    }

    /**
     * @Route("/change_password", name="user_auth_change_password")
     * @Template("ManagerUserBundle:Auth:change_password.html.twig")
     */
    public function changePasswordAction(Request $request)
    {
        $username = $this->getUser()->getUsername();
        $error    = $request->get('error');
        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneByUsername($username);
        if (empty($entity)) {
            throw new \Exception('没有此用户');
        }


        return array('entity' => $entity, 'error' => $error);
    }

    /**
     * @Route("/modify_password", name="user_auth_modify_password")
     */
    public function modifyPasswordAction(Request $request)
    {
        $username = $this->getUser()->getUsername();
        $oldPassword = $request->get('old_password');
        $newPassword = $request->get('new_password');

        if (empty($oldPassword) || empty($newPassword)) {
            throw new \Exception('传入参数为空');
        }

        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneByUsername($username);
        if (empty($entity)) {
            throw new \Exception('没有此用户');
        }

        if (md5($oldPassword) != $entity->getPassword()) {
            $response = $this->redirect($this->generateUrl('user_auth_change_password', array('error' => '旧密码错误')));
            return $response;
        }

        $entity->setPassword(md5($newPassword));
        $em->persist($entity);
        $em->flush();

        $response = $this->redirect($this->generateUrl('user_logout'));
        return $response;
    }

    /**
     * @Route("/delete", name="user_auth_delete")
     */
    public function deleteAction(Request $request)
    {
        $aid = $request->get('a_id');
        $em = $this->getEM();
        $rp = $em->getRepository('ManagerUserBundle:Auth');
        $entity = $rp->findOneById($aid);
        if (empty($entity)) {
            throw new \Exception('没有此用户');
        }
        $em->remove($entity);
        $em->flush();

        $response = $this->redirect($this->generateUrl('user_auth_list'));
        return $response;
    }

    private function getEM()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * 获取查询参数
     */
    private function getQueryParams($request)
    {
        $queryParams = array();

        if ($request->get('username')) {
            $queryParams['username'] = $request->get('username');
        }

        return $queryParams;
    }
}
