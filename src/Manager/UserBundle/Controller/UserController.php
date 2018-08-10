<?php

namespace Manager\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Manager\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * 登录
     * @Route("/login", name="user_login")
     * @Template("ManagerUserBundle:User:login.html.twig")
     */
    public function loginAction()
    {
        $request  = $this->getRequest();
        $username = $request->get('username');
        $password = $request->get('password');
        if (empty($username) || empty($password)) {
            return array();
        }

        $session = new Session();
        $session->start();

        $em = $this->getDoctrine()->getEntityManager();

        $rp = $em->getRepository('ManagerUserBundle:User');
        $isUser = $rp->isAvailableUser($username);
        if (!$isUser) {
            return array('loginfailed' => '1');
        }

        $isRightPassword = $rp->isRightPassword($username, $password);
        if (!$isRightPassword) {
            return array('loginfailed' => '1');
        }

        $session->set('user', array(
            'user'    => $username,
            'display' => $username,
            'mail'    => $username,
            'timestamp' => time()
        ));

        $response = $this->redirect($this->generateUrl('user_homepage'));
        return $response;
    }
    
    /**
     * 退出
     * @Route("/logout", name="user_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    /**
     * @Route("/homepage", name="user_homepage")
     * @Template("ManagerUserBundle:User:homepage.html.twig")
     */
    public function homepageAction()
    {
        return array();
    }

    /**
     * @Route("/list", name="user_user_list")
     * @Template("ManagerUserBundle:User:list.html.twig")
     */
    public function listAction()
    {
        $start = 1;
        $limit = 10;

        $request = $this->getRequest();

        $roleList = $this->fetchRoleList();
        list($total, $data) = $this->fetchData($start, $limit);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        return array (
            "total"      => $total,
            "totalPages" => $totalPages,
            "start"      => $start,
            "limit"      => $limit,
            'data'       => $data,
            'roleList'   => $roleList,
        );
    }

    /**
     * @Route("/add", name="user_user_add")
     * @Template("ManagerUserBundle:User:add.html.twig")
     */
    public function addAction()
    {
        $request  = $this->getRequest();
        $id       = $request->get('id');
        $confirm  = $request->get('confirm');
        $operator = $this->getUser()->getUserName();

        try {
            $em = $this->getDoctrine()->getEntityManager();

            if ($id) {
                $User = $em->getRepository('ManagerUserBundle:User')->find($id);
            } else {
                $User = new User();
            }

            $roleList = $this->fetchRoleList();

            if ($confirm) {
                $username = $request->get('username');
                $em = $this->getDoctrine()->getEntityManager();
                $entity = $em->getRepository('ManagerUserBundle:Auth')->findOneByUsername($username);
                if (empty($entity))
                    return new JsonResponse(array('code'=>1, 'msg'=>'用户不存在'.PHP_EOL.'请先到账号管理页面中添加用户'));

                $now = new \DateTime();
                $User->setUsername($request->get('username'));
                $User->setRoleId($request->get('roleId'));
                $User->setOperator($operator);
                $User->setUpdateTime($now);
                if (!$id) {
                    $User->setCreateTime($now);
                }
                $em->persist($User);
                $em->flush();

                return new JsonResponse(array("code" => 0, "msg" => "success"));
            } else {
                return array('user' => $User, 'roleList' => $roleList);
            }
        } catch (\Exception $e) {
            $msg = $e->getFile() . ":" . $e->getLine() . ":" . $e->getMessage();
            $this->get("logger")->addError($msg);
            return new JsonResponse(array("code" => 2, "msg" => '该用户已存在此角色'));
        }
    }

    /**
     * @Route("/query", name="user_user_query")
     * @Template("ManagerUserBundle:User:query_result.html.twig")
     */
    public function queryAction()
    {
        $request     = $this->getRequest();
        $queryParams = $this->getQueryParams($request);

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;

        list($total, $data) = $this->fetchData($start, $limit, $queryParams);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        return array (
            "total"      => $total,
            "totalPages" => $totalPages,
            "start"      => $start,
            "limit"      => $limit,
            'data'       => $data,
        );
    }

    /**
     * @Route("/delete", name="user_user_delete")
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = $request->get('id');
        $em      = $this->getDoctrine()->getEntityManager();
        $User    = $em->getRepository('ManagerUserBundle:User')->find($id);

        $em->remove($User);
        $em->flush();

        return new JsonResponse(array("code" => 0,"msg" => "success"));
    }

    /**
     * 获取数据
     *
     * @param   int   $start
     * @param   int   $limit
     * @param   array $queryParams
     * @return  array 例如：('total' => $total, 'data' => $data)
     */
    private function fetchData($start, $limit, $queryParams = array())
    {
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('ManagerUserBundle:User')->getList($start, $limit, $queryParams);

        return $result;
    }

    private function fetchRoleList()
    {
        $em       = $this->getDoctrine()->getEntityManager();
        $roleList = $em->getRepository('ManagerUserBundle:Role')->getRoleList();

        return $roleList;
    }

    /**
     * 获取查询参数
     *
     * @param   object   $request
     * @return  array
     */
    private function getQueryParams($request)
    {
        $queryParams = array();

        if ($request->get('operator')) {
            $queryParams ['operator'] = $request->get('operator');
        }
        if ($request->get('startTime')) {
            $queryParams ['startTime'] = $request->get('startTime');
        }
        if ($request->get('endTime')) {
            $queryParams ['endTime'] = $request->get('endTime');
        }
        if ($request->get('username')) {
            $queryParams ['username'] = $request->get('username');
        }
        if ($request->get('roleId')) {
            $queryParams ['roleId'] = $request->get('roleId');
        }

        return $queryParams;
    }
}
