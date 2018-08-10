<?php

namespace Manager\UserBundle\Security\Authentication\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Manager\UserBundle\Security\Token\ManagerUserToken;
use Manager\UserBundle\Entity\OptLog;

/**
 * 权限管理的voter 
 */
class AuthVoter implements VoterInterface
{

    private $container;
    private $roleService;
    private $logger;

    public function __construct ( ContainerInterface $container )
    {
        $this->container = $container;
        //$this->roleService = $this->container->get("user.role_service");
    }

    public function supportsAttribute ( $attribute )
    {
        // you won't check against a user attribute, so return true
        return true;
    }

    public function supportsClass ( $class )
    {
        return $class === 'Manager\UserBundle\Security\Token\ManagerUserToken';
    }

    private function getEnvAuth ()
    {
        $envAuth = 0; //是否需要鉴权
        try
        {
            $envAuth = ( int ) $this->container->getParameter ( "env.auth" );
        }
        catch ( \Exception $e )
        {
            
        }
        return $envAuth;
    }

    public function vote ( TokenInterface $token, $object, array $attributes )
    {
        $username = $token->getUsername ();
        $route = $object->attributes->get ( "_route" );

        $envAuth = $this->getEnvAuth ();
        if ( $envAuth != 0 )
        {//需要鉴权（测试环境，线上环境，都需要鉴权）
            $auth = $this->checkAuth ( $username, $route, $access_route );
        }
        else
        {
            $access_route = $this->container->get ( 'doctrine' )->getRepository ( 'ManagerUserBundle:Path' )->getMenu ();
            $auth = VoterInterface::ACCESS_ABSTAIN;
        }

        if ( $auth != VoterInterface::ACCESS_DENIED )
        {
            $this->updateLeftMenu ( $access_route, $route );
        }

        if ( $auth != VoterInterface::ACCESS_DENIED )
        {
            $this->logAccess ( $username, $route );
        }

        return $auth;
    }

    private function logAccess ( $username, $route )
    {
        $request = $this->container->get ( 'request' );
        $content = $request->getContent ();
        if ( !empty ( $content ) )
        {
            $em = $this->container->get ( 'doctrine' )->getEntityManager ();
            $entity = new OptLog();
            $entity->setOperator ( $username );
            $entity->setRoute ( $route );
            $entity->setContent ( $content );
            $em->persist ( $entity );
            $em->flush ();
        }
    }

    //更新左侧的菜单
    private function updateLeftMenu ( $access_route, $route )
    {
        $current_firstMenu = 0;
        foreach ( $access_route as $value )
        {
            if ( $value[ 'route' ] == $route )
            {
                $current_firstMenu = $value[ 'firstMenu' ];
            }
        }
        $this->container->get ( "twig" )->addGlobal ( 'left_menu', $access_route );
        $this->container->get ( "twig" )->addGlobal ( 'current_route', $route );
        $this->container->get ( "twig" )->addGlobal ( 'current_firstMenu', $current_firstMenu );
    }

    //调用服务检查权限
    private function checkAuth ( $username, $route, &$access_route )
    {
        static $user = array ();
        if ( !isset ( $user[ $username ] ) )
        {
            $result = $this->container->get ( 'doctrine' )->getRepository ( 'ManagerUserBundle:User' )->findUserRole ( $username );
            foreach ( $result as $value )
            {
                $user[ $username ][] = $value[ 'role_id' ];
            }
        }

        if ( !isset ( $user[ $username ] ) )
        {
            return VoterInterface::ACCESS_DENIED;
        }

        static $roleRoutes = array ();
        if ( !isset ( $roleRoutes[ $username ] ) )
        {
            $roleRoutes[ $username ] = $this->container->get ( 'doctrine' )->getRepository ( 'ManagerUserBundle:Role' )->findRoleAccessPath ( $user[ $username ] );
        }

        if ( !$roleRoutes[ $username ] )
        {
            return VoterInterface::ACCESS_DENIED;
        }

        $routeids = array ();
        foreach ( $roleRoutes[ $username ] as $value )
        {
            $routeids[] = $value[ 'route_id' ];
        }

        $access_route = $routeList = array ();
        $result = $this->container->get ( 'doctrine' )->getRepository ( 'ManagerUserBundle:Path' )->getMenu ();
        foreach ( $result as $value )
        {
            if ( in_array ( $value[ 'id' ], $routeids ) )
            {
                $access_route[] = $value;
                $routeList[] = $value[ 'route' ];
            }
        }

        if ( $route != 'manager_homepage' && !in_array ( $route, $routeList ) )
        {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

}
