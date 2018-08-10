<?php
/**
 * 监听每一个request事件 
 */
namespace Manager\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManagerRequestListener
{
    private $container;
    private $log;
    public function __construct($container)
    {
        $this->container = $container;
        $this->log = $container->get("monolog.logger.event");
    }
    //监控系统的request事件
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }
        $logArr = array();
        $request = $event->getRequest();
        $logArr['route'] = $request->attributes->get("_route");
        $logArr['query'] = $request->query->all();
        $logArr['post'] = $request->request->all();
        $logArr['path_info'] = $request->getPathInfo();
        $logArr['remote_addr'] = $request->server->get("REMOTE_ADDR");    
        $logArr['user'] = $this->getUser();
        $line = json_encode($logArr);
        $this->log->info($line);
    }
    
    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            return null;
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }
        
        if (!is_object($user = $token->getUser())) {
            return null;
        }
        $arr = array(
            "username" => $user->getUsername(),
            "roles" => $user->getRoles(),
        );
        return $arr;
    }
}