<?php
namespace Manager\UserBundle\Security\Firewall;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Manager\UserBundle\Security\Authentication\Token\ManagerUserToken;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ManagerUserListener implements ListenerInterface
{
    private $container;
    private $securityContext;
    private $managerUserProvider;
    private $log;

    public function __construct(SecurityContextInterface $securityContext, ContainerInterface $container, $managerUserProvider)
    {
        $this->container = $container;
        $this->securityContext = $securityContext;
        $this->managerUserProvider = $managerUserProvider;
        $this->log = $this->container->get("logger");
    }
    
    private function getTokenFromLogin($arr) {
        $this->log->info("getTokenFromLogin: ".print_r($arr, true));
        
        $token = new ManagerUserToken();
        $token->setUname($arr['user']);
        $token->setDisplayName($arr['display']);
        $token->setEmail($arr['mail']);
        $token->setAuthenticated(false);
        return $token;
    }

    public function handle(GetResponseEvent $event)
    {
        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof ManagerUserToken && $token->isAuthenticated()) {
                return;
            }
        }
        try {
            $sesstion = new Session();
            $arr = $sesstion->get('user');
            
            if (empty($arr)) {
                header("Location: " . $this->container->get('router')->generate('user_login'));exit;
            }

            $token = $this->getTokenFromLogin($arr);
            $authToken = $this->managerUserProvider->authenticate($token);
            $this->securityContext->setToken($authToken);
            //$this->log->addInfo("securityContext->setToken: \n".print_r($authToken, true));
        } catch (AuthenticationException $e) {
            // To deny the authentication clear the token. This will redirect to the login page.
            // $this->securityContext->setToken(null);
            // return;

            // Deny authentication with a '403 Forbidden' HTTP response
            $this->securityContext->setToken(null);
            $msg = $e->getFile().":".$e->getLine().":".$e->getMessage();
            $this->log->err($msg);
            $response = new Response("403 Forbidden: ".$e->getMessage());
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }
}
