<?php

namespace CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use GestionBundle\Entity\ConfiguracionGlobal;
/**
 * @Route("/")
 * @Security("has_role('ROLE_USER')")
 */
class DefaultController extends Controller
{    
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function homepageAction(Request $request)
    {
        //$em = $this->getDoctrine()->getManager();
        //$gConfig = $this->get('app.plenusConfig')->getConfiguracion();
        //var_dump($gConfig->isNewAccountActive());
        //$gConfig = $this->get('app.plenusConfig')->setConfiguracion();
        //var_dump($gConfig->isNewAccountActive());
        //die;
        return array('numFrase1'=>rand(1,3));
    }
}