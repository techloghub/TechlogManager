<?php

namespace Manager\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/homepage")
 */
class DefaultController extends Controller
{
	/**
	 * @Route("/index")
	 * @Template("ManagerApiBundle:Default:index.html.twig")
	 */
    public function indexAction()
    {
        return array('name' => 'hello');
    }
}
