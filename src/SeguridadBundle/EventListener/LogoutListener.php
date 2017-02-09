<?php

namespace SeguridadBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use SeguridadBundle\Entity\Log;
//handlers: [mybundle_logoutlistener]
class LogoutListener implements LogoutSuccessHandlerInterface
{
    private $security;
    protected $em;
    protected $router;
    
    public function __construct(TokenStorage $security, EntityManager $em,$router)
    {
        $this->security = $security;
        $this->em = $em;
        $this->router = $router;
    }
    
    public function onLogoutSuccess(Request $request)
    {
        echo "lalala";
        die;
        //if ($this->security){
        //    $user = $this->security->getToken()->getUser();
        //    if ($user){
        //        $log = new Log();
        //        $log->setCreatedBy($user);
        //        $log->setIp($request->headers->get('host'));
        //        $log->setActivity('logout');
        //        $this->em->persist($log);
        //        $user->setLogged(false);
        //        $this->em->flush($user);
        //    }
        //}
        //return new RedirectResponse($this->router->generate('_login'));
    }
}