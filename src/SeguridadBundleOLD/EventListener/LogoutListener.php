<?php

namespace SeguridadBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use SeguridadBundle\Entity\Logs;

class LogoutListener implements LogoutSuccessHandlerInterface
{
    private $security;
    protected $entityManager;
    protected $router;
    
    public function __construct(SecurityContext $security, EntityManager $entityManager,$router)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }
    
    public function onLogoutSuccess(Request $request)
    {
        if ($this->security){
            $user = $this->security->getToken()->getUser();
            
            $this->entityManager->persist(new Logs($user,'0.0.0.0/0','logout'));
            $this->entityManager->flush($user);
        }    
        $response =  new RedirectResponse($this->router->generate('_login'));

        return $response;
    }
}