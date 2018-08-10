<?php

namespace Manager\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Manager\UserBundle\Entity\Role;
use Manager\UserBundle\Entity\Block;

/**
 * @Route("/role")
 */
class RoleController extends Controller
{
    /**
     * @Route("/list", name="user_role_list")
     * @Template("ManagerUserBundle:Role:list.html.twig")
     */
    public function listAction()
    {
        $start = 1;
        $limit = 10;

        $firstMenu = $this->fetchMenu();

        $em = $this->getDoctrine()->getManager();
        list($total, $data) = $em->getRepository('ManagerUserBundle:Role')->getList($start, $limit);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        $result = array (
            "total"      => $total,
            "totalPages" => $totalPages,
            "start"      => $start,
            "limit"      => $limit,
            'data'       => $data,
            'firstMenu'  => $firstMenu,
        );

        return $result;
    }

    /**
     * @Route("/query", name="user_role_query")
     * @Template("ManagerUserBundle:Role:query_result.html.twig")
     */
    public function queryAction()
    {
        $request     = $this->getRequest();
        $queryParams = $this->getQueryParams($request);

        $start =(int) $request->get("start");
        $limit =(int) $request->get("limit");

        $start = $start <= 0 ? 1 : $start;
        $limit = $limit <= 0 ? 10 : $limit;

        $em = $this->getDoctrine()->getManager();
        list($total, $data) = $em->getRepository('ManagerUserBundle:Role')->query($start, $limit, $queryParams);

        $totalPages = (int)(($total + $limit - 1) / $limit);

        return array(
            "total"      => $total,
            "totalPages" => $totalPages,
            "start"      => $start,
            "limit"      => $limit,
            'data'       => $data,
        );
    }

    /**
     * @Route("/add", name="user_role_add")
     * @Template("ManagerUserBundle:Role:add.html.twig")
     */
    public function addAction()
    {
        $request     = $this->getRequest();
        $id          = $request->get('id');
        $name        = $request->get('name');
        $confirm     = $request->get('confirm');
        $operator    = $this->getUser()->getUserName();
        $now         = date('Y-m-d H:i:s');

        $exist_route = $all_route = array();

        try {
            $em = $this->getDoctrine()->getEntityManager();

            if ($id) {
                $Role = $em->getRepository('ManagerUserBundle:Role')->findRoleByRoleId($id);
            } else {
                $Role = array();
            }

            if ($confirm) {
                $role_data_create = array('name' => $name, 'operator' => $operator, 'update_time' => $now, 'create_time' => $now);
                $role_data_update = array('name' => $name, 'operator' => $operator, 'update_time' => $now);

                if (!$id) {
                    $result = $em->getRepository('ManagerUserBundle:Role')->findRoleByRoleName($name);
                    if (!$result) {
                        $role_id = $em->getRepository('ManagerUserBundle:Role')->addRole($role_data_create);
                    } else {
                        return new JsonResponse(array("code" => 2, "msg" => "存在相同的角色"));
                    }
                } else {
                    $role_id     = $Role['id'];
                    $exist_route = $Role['route'];
                    $em->getRepository('ManagerUserBundle:Role')->updateRole($role_data_update, array('id' => $id));
                }

                $post_route = (array)$request->get('route');

                foreach (array_diff($post_route, $exist_route) as $route_id) {
                    $em->getRepository('ManagerUserBundle:Role')->addAccess(array('role_id' => $role_id, 'route_id' => $route_id));
                }

                foreach (array_diff($exist_route, $post_route) as $route_id) {
                    $em->getRepository('ManagerUserBundle:Role')->deleteAccess(array('role_id' => $role_id, 'route_id' => $route_id));
                }

                return new JsonResponse(array("code" => 0, "msg" => "success"));
            } else {
                $result = $this->fetchMenu();
                foreach ($result as $value) {
                    $id   = $value['id'];
                    $name = $value['name'];
                    $first  = $value['firstMenu'];
                    $second = $value['secondMenu'];

                    if ($first == 0) {
                        $firstMenuList[] = array('id' => $id, 'name' => $name);
                    } else if ($second == 0) {
                        $secondMenuList[$first]['second'][] = array('id' => $id, 'name' => $name);
                    } else {
                        $thirdMenuList[$first]['third'][] = array('id' => $id, 'name' => $name);
                    }
                }

                return array('role' => $Role, 'firstMenuList' => $firstMenuList, 'secondMenuList' => $secondMenuList, 'thirdMenuList' => $thirdMenuList);
            }
        } catch (\Exception $e) {
            $msg = $e->getFile() . ":" . $e->getLine() . ":" . $e->getMessage();
            $this->get("logger")->addError($msg);
            return new JsonResponse(array("code" => 2, "msg" => $msg));
        }
    }

    /**
     * @Route("/delete", name="user_role_delete")
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = $request->get('id');
        $em      = $this->getDoctrine()->getEntityManager();
        $Block   = $em->getRepository('ManagerUserBundle:Role')->find($id);

        $em->remove($Block);
        $em->flush();

        return new JsonResponse(array ("code" => 0, "msg" => "success"));
    }

    private function fetchMenu()
    {
        $em     = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('ManagerUserBundle:Path')->getMenu();

        return $result;
    }

    /**
     * 获取查询参数
     * 
     * @param object $request
     * @return array
     */
    private function getQueryParams($request)
    {
        $queryParams = array();

        if ($request->get('operator')) {
            $queryParams ['operator'] = $request->get('operator');
        }
        if ($request->get('name')) {
            $queryParams ['name'] = $request->get('name');
        }
        if ($request->get('firstMenu')) {
            $queryParams ['firstMenu'] =(int) $request->get('firstMenu');
        }
        if ($request->get('secondMenu')) {
            $queryParams ['secondMenu'] =(int) $request->get('secondMenu');
        }

        return $queryParams;
    }
}