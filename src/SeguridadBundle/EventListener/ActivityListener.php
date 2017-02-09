<?php
namespace SeguridadBundle\EventListener;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Doctrine\ORM\EntityManager;
use SeguridadBundle\Entity\Usuario;
use SeguridadBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listener que registra toda actividad de usuarios
 */
class ActivityListener
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

    /**
    * Chequea que sea un usuario activo
    * Chequea que el usuario no tenga q cambiar la clave
    * Update de la ultima actividad del usuario
    * @param FilterControllerEvent $event
    */
    public function onCoreController(FilterControllerEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() == HttpKernel::MASTER_REQUEST) {
			$request = $event->getRequest();
			// Ignore some routes for not enter into a bucle
			$routes = ['_reset_password_step1','_changePassword','_checkUserData','_login','_security_check','_new_account','_wdt','_new_account_check_data'];
			if ( !in_array($request->attributes->get('_route'), $routes)){
				// Check token authentication availability
				$user = $this->securityContext->getToken()->getUser();
				if ($this->securityContext->getToken() && is_object($user))
				{
					$session = $request->getSession();
					if (!$session->has('haveUser')){
						// store an attribute for reuse during a later user request
						$session->set('haveUser', $user->getId());
						$sessionPDO = $this->em->getRepository('SeguridadBundle:Sessions')->findOneBy(array("sessId" => $session->getId()));
						$sessionPDO->setCreatedBy($user);
					}
					$user->setLastActivity(new \DateTime());
					$this->em->flush();

					if ($user->getChangePassword()){
						$url = $this->router->generate('_changePassword');     
						$event->setController(function() use ($url) {
							return new RedirectResponse($url);
						});          
					}
					if ($user->getCheckData()){
						$url = $this->router->generate('_checkUserData');
						$event->setController(function() use ($url) {
							return new RedirectResponse($url);
						});          
					}			
		
					$log = new Log($user,$request->getClientIp(),'activity',$request->attributes->get('_route'));
					$log->setDescription($request->getPathInfo());
		
					$this->em->persist($log);
					//SI no se puede hacer el flush redirecciona al logout
					try {
						$this->em->flush();
					}
					catch(\Exception $e){
						$response = new RedirectResponse($this->router->generate('_logout'));
					}				
				}else{
					/* Atiende requerimientos Ajax para usuarios no logueados */
					if ($request->isXmlHttpRequest()) {
						echo "<script>sweetAlert({title: 'Error!',text: 'Su sesión ah expirado.<br>Vuelva a ingresar su usuario y contraseña para continuar.',type: 'error',html: true}, function(){ location.reload(); });</script>";die();
					}
				}
			}
		}
		return;
    }
}