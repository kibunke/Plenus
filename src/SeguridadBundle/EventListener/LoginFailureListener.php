<?php

namespace SeguridadBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Security;
//use Symfony\Component\Security\Core\SecurityContextInterface;

use Doctrine\ORM\EntityManager;
use SeguridadBundle\Entity\Log;

class LoginFailureListener//  extends DefaultAuthenticationFailureHandler
{
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/** @var Symfony\Component\Routing\Router */
	protected $router;
	
	protected $request;
	
	//"@http_kernel", "@security.http_utils",@doctrine.orm.entity_manager,@router
    public function __construct($kernel, $utils, EntityManager $entityManager, Router $router, $request)
    {
        $this->em = $entityManager;
        $this->router = $router;
		$this->request = $request;
    }	

	public function onAuthenticationFailure(AuthenticationFailureEvent $event)
	{
		$request = $this->request->getMasterRequest();
		$request->getSession()->set(Security::AUTHENTICATION_ERROR, $event->getAuthenticationException());
        if ($request->request->has('_username')) {
			$user = $this->em->getRepository('SeguridadBundle:Usuario')->findOneBy(array("username" => $request->request->get('_username')));
        } else {
            $user = null;
        }
		$log = new Log($user,$request->getClientIp(),'loginFailure',null);
        if ($user){
			$log->setActivity('wrongPassword');
		}else{
			$log->setActivity('wrongUser');
			$log->setDescription("Intento de login con usuario incorrecto : ".$request->request->get('_username'));
		}
		$this->em->persist($log);
		$this->em->flush($log);
        return new RedirectResponse($this->router->generate('_login'));   
	}
}