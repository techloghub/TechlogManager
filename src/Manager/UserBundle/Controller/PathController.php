<?php

namespace Manager\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Manager\UserBundle\Entity\Path;

/**
 * @Route("/path")
 */
class PathController extends Controller
{
    /**
     * @Route("/list", name="user_path_list")
     * @Template("ManagerUserBundle:Path:list.html.twig")
     */
    public function listAction()
    {
        $start = 1;
        $limit = 10;

        $request = $this->getRequest();

        $menu = $this->fetchMenu();

        list($total, $data) = $this->fetchData($start, $limit);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        return array (
            "total"      => $total,
            "totalPages" => $totalPages,
            "start"      => $start,
            "limit"      => $limit,
            'data'       => $data,
            'menu'       => $menu,
       );
    }

    /**
     * @Route("/menu", name="user_path_menu")
     */
    public function menuAction()
    {
        $request = $this->getRequest();

        $params = array();

        $params ['firstMenu']  = (int)$request->get('firstMenu');
        $params ['secondMenu'] = (int)$request->get('secondMenu');

        $em     = $this->getDoctrine()->getManager();
        $result = $em->getRepository('ManagerUserBundle:Path')->getMenu($params);

        return new JsonResponse(array("code" => 0, "msg" => "success", "result" => $result));
    }

    /**
     * @Route("/query", name="user_path_query")
     * @Template("ManagerUserBundle:Path:query_result.html.twig")
     */
    public function queryAction()
    {
        $request     = $this->getRequest();
        $queryParams = $this->getQueryParams($request);

        $start = (int)$request->get("start");
        $limit = (int)$request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;

        list($total, $data ) = $this->fetchData($start, $limit, $queryParams);

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
     * @Route("/add", name="user_path_add")
     * @Template("ManagerUserBundle:Path:add.html.twig")
     */
    public function addAction()
    {
        $request  = $this->getRequest();
        $id       = $request->get('id');
        $confirm  = $request->get('confirm');
        $operator = $this->getUser()->getUserName();

        $em = $this->getDoctrine()->getEntityManager();

        try {
            if ($id) {
                $path = $em->getRepository('ManagerUserBundle:Path')->find($id);
            } else {
                $path = new Path();
            }

            $menu = $this->fetchMenu();

            if ($confirm) {
                $route      = $request->get('route');
                $firstMenu  = (int)$request->get('firstMenu');
                $secondMenu = (int)$request->get('secondMenu');

                $result = $em->getRepository('ManagerUserBundle:Path')->findRoute($route, $secondMenu);

                if ($route && $result && (!$id || ($id && $result['id'] != $id))) {
                    return new JsonResponse(array("code" => 2, "msg" => '路由已经存在！'));
                }

                $now = new \DateTime();
                $em  = $this->getDoctrine()->getEntityManager();
                $path->setName($request->get('name'));
                $path->setRoute($route);
                $path->setRemark($request->get('remark'));
                $path->setFirstMenu($firstMenu);
                $path->setSecondMenu($secondMenu);
                $path->setOperator($operator);
                $path->setUpdateTime($now);
                if (!$id) {
                    $path->setCreateTime($now);
                }
                $em->persist($path);
                $em->flush();

                return new JsonResponse(array("code" => 0, "msg" => "success"));
            } else {
                return array('path' => $path, 'menu' => $menu);
            }
        } catch (\Exception $e) {
            $msg = $e->getFile() . ":" . $e->getLine() . ":" . $e->getMessage();
            $this->get("logger")->addError($msg);
            return new JsonResponse(array("code" => 2, "msg" => $msg));
        }
    }

    /**
     * @Route("/delete", name="user_path_delete")
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = $request->get('id');
        $em      = $this->getDoctrine()->getEntityManager();
        $path   = $em->getRepository('ManagerUserBundle:Path')->find($id);

        $em->remove($path);
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
        $em     = $this->getDoctrine()->getManager();
        $result = $em->getRepository('ManagerUserBundle:Path')->getList($start, $limit, $queryParams);

        return $result;
    }

    private function fetchMenu()
    {
        $em     = $this->getDoctrine()->getManager();
        $result = $em->getRepository('ManagerUserBundle:Path')->getMenu();

        return $result;
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
        if ($request->get('firstMenu')) {
            $queryParams ['firstMenu'] = (int)$request->get('firstMenu');
        }
        if ($request->get('secondMenu')) {
            $queryParams ['secondMenu'] = (int)$request->get('secondMenu');
        }
        if ($request->get('name')) {
            $queryParams ['name'] = $request->get('name');
        }

        return $queryParams;
    }
}