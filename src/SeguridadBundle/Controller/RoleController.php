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

use SeguridadBundle\Form\RoleType;
use SeguridadBundle\Entity\Role;

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
    
    /**
     * @Route("/{id}/edit", name="role_edit", defaults={"id":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request)
    {
        
    }
    
    /**
     * @Route("/new", name="role_new")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Role:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $entity = new Role();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if($request->getMethod() == 'POST')
        {
            if ($form->isSubmitted() && $form->isValid())
            {
                $em  = $this->getDoctrine()->getManager();
                print_r($form->getData());die;
                return JsonResponse(array('datos cargados con éxito'));
            }
            print_r($form->getData());die;
            return JsonResponse(array('error en los datos del formulario'));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
     /**
     * Creates a form to create a Email entity.
     *
     * @param Email $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Role $entity)
    {
        $form = $this->createForm(RoleType::class, $entity);
    
        return $form;
    }
}