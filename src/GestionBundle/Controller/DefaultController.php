<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Default controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage_gestion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    /**
     * @Route("/configuracion", name="gestion_configuracion")
     * @Method("GET")
     * @Template()
     */
    public function configuracionAction()
    {
        return array();
    }    
}
