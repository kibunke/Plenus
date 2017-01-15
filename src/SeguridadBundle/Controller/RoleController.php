<?php

namespace SeguridadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Role controller.
 *
 * @Route("/system/role")
 */
class RoleController extends Controller
{
    
    
    /**
     * @Route("/list", name="role_list")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="role_list_datatable")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listDataTableAction(Request $request)
    {
        $user   = $this->getUser();
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('SeguridadBundle:Role')->datatable($request->request);

        $data=array(
            "draw"=> $request->request->get('draw'),
            "recordsTotal"=> $filter['total'],
            "recordsFiltered"=> $filter['filtered'],
            "data"=> $filter['rows'],
        );
        
        return new JsonResponse($data);
    }
}