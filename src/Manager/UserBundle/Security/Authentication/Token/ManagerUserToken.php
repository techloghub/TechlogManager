<?php
/**
 * manager登陆用户的token 
 */
namespace Manager\UserBundle\Security\Authentication\Token;
use Symfony\Component\Security\Core\Exception;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ManagerUserToken extends AbstractToken {
    private $uname = "";
    private $displayName = "";
    private $email = "";
    public function getCredentials() {
        return "";
    }
    public function setUname($uname) {
        $this->uname = $uname;
    }
    public function getUname() {
        return $this->uname;
    }
    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function getDisplayName() {
        return $this->displayName;
    }
    public function getEmail() {
        return $this->email;
    }
}