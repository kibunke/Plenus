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
        $roles  = array();
        foreach($filter['rows'] as $role)
        {
            $roles[] = array(
                               'id'          => $role->getId(),
                               'name'        => $role->getName(),
                               'description' => $role->getDescription(),
                               'isActive'    => $role->getIsActive(),
                               'perfiles'    => $role->getPerfiles()->count()
                            );
        }
        $data=array(
            "draw"            => $request->request->get('draw'),
            "recordsTotal"    => $filter['total'],
            "recordsFiltered" => $filter['filtered'],
            "data"            => $roles,
        );
        
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/{role}/edit", name="role_edit", defaults={"role":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Role:edit.html.twig")
     */
    public function editAction(Request $request,Role $role)
    {
        $nombreOrignal = $role->getName();
        $form          = $this->createCreateForm($role);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $role->setName($nombreOrignal);
            $role->setModifiedBy($this->getUser());
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Rol modificado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Ya existe un Rol con ese nombre'));
            }
        }
        return array(
            'entity' => $role,
            'form'   => $form->createView(),
        );
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
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Rol creado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Ya existe un Rol con ese nombre'));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{role}/delete", name="role_delete", defaults={"role":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Role:delete.html.twig")
     */
    public function deleteAction(Request $request,Role $role)
    {
        $nombreOrignal = $role->getName();
        $form          = $this->createDeleteForm($role);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            if($role->getPerfiles()->count() > 0)
            {
                return new JsonResponse(array('resultado' => 2, 'mensaje' => 'No pudo ser eliminado el Rol porque tiene Perfiles asignados'));
            }
            
            $em  = $this->getDoctrine()->getManager();
            $em->remove($role);
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Rol eliminado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'No pudo ser eliminado el Rol'));
            }
        }
        return array(
            'entity' => $role,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a Email entity.
     *
     * @param Role $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Role $entity)
    {
        $form = $this->createForm(RoleType::class, $entity);
    
        return $form;
    }
    
    /**
     * Creates a form to delete a role entity.
     *
     * @param $role The role entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Role $role)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('role_delete', array('id' => $role->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}