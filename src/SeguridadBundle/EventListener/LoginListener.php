<?php

namespace SeguridadBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

use Doctrine\ORM\EntityManager;
use SeguridadBundle\Entity\Log;

class LoginListener //implements AuthenticationSuccessHandlerInterface
{	
    protected $securityContext;
    protected $em;
    protected $router;

    public function __construct(TokenStorage $securityContext, EntityManager $entityManager, Router $router )
    {
        $this->securityContext = $securityContext;
        $this->em = $entityManager;
        $this->router = $router;
    }
	
	//public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	public function onAuthenticationSuccess(InteractiveLoginEvent $event)
	{
		$request = $event->getRequest();
		$user = $this->securityContext->getToken()->getUser();
		$error = false;
		$ip = $this->getIp();
		$log = new Log($user,$ip,'loginSuccess','login');
		if ($request->getSession()->get('captcha') != $request->request->get('_input_captcha'))
		{
			//Redirect to logout by Captcha error
			$log->setActivityGroup('loginFailure')->setActivity('wrongCaptcha')->setDescription('Captcha: '.$request->getSession()->get('captcha').' Input value: '.$request->request->get('_input_captcha'));
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, (object) array('message' => 'Captcha inválido'));
			$request->getSession()->getFlashBag()->add('error', 'Captcha inválido.');
			$error = true;
		}elseif(!$user->getIsActive()){
			//Redirect to logout by Inactive User
			$log->setActivityGroup('loginFailure')->setActivity('userInactive')->setDescription('This user isnt active');
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, (object) array('message' => 'Usuario inactivo'));
			$request->getSession()->getFlashBag()->add('error', 'Usuario inactivo.');
			$error = true;
		}
		$this->em->persist($log);
		//Try to flush. If failure then redirec to logout
		try {
			$this->em->flush();
		}
		catch(\Exception $e){
			$error = true;
		}
		// if error invalidate session token  
		if ($error){
			$this->securityContext->setToken(null);
			//don't must invalidate all session because it's have flash messages
			//$request->getSession()->invalidate();
		}
	}
	private function getIp()
	{
		$ip = [];
		$client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP)){ $ip[]= $client; }
		if(filter_var($forward, FILTER_VALIDATE_IP)){ $ip[]= $forward; }
		$ip []= $remote;
		return implode("|",$ip);
	}
}