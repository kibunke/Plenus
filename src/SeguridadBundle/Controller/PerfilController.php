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

use SeguridadBundle\Form\PerfilType;
use SeguridadBundle\Entity\Perfil;

/**
 * Perfil controller.
 *
 * @Route("/system/perfil")
 */
class PerfilController extends Controller
{
    /**
     * @Route("/list", name="perfil_list")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="perfil_list_datatable")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listDataTableAction(Request $request)
    {
        $user   = $this->getUser();
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('SeguridadBundle:Perfil')->datatable($request->request);
        $perfiles = [];
        foreach($filter['rows'] as $perfil)
        {
            $perfiles[] = array( 'id'          => $perfil->getId(),
                                 'name'        => $perfil->getName(),
                                 'legend'      => $perfil->getLegend(),
                                 'description' => $perfil->getDescription(),
                                 'isActive'    => $perfil->getIsActive(),
                                 'roles'       => $perfil->getRoles()->count(),
                                 'usuarios'    => $perfil->getUsuarios()->count(),
                                );
        }
        $data=array(
            "draw"=> $request->request->get('draw'),
            "recordsTotal"=> $filter['total'],
            "recordsFiltered"=> $filter['filtered'],
            "data"=> $perfiles,
        );
        
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/{perfil}/edit", name="perfil_edit", defaults={"perfil":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Perfil:edit.html.twig")
     */
    public function editAction(Request $request,Perfil $perfil)
    {
        $nombreOrignal = $perfil->getName();
        $form          = $this->createCreateForm($perfil);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em  = $this->getDoctrine()->getManager();
            $perfil->setName($nombreOrignal);
            $perfil->setModifiedBy($this->getUser());
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Rol modificado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Ya existe un Rol con ese nombre'));
            }
        }
        return array(
            'entity' => $perfil,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * @Route("/new", name="perfil_new")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Perfil:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $entity = new Perfil();
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
     * @Route("/{perfil}/delete", name="perfil_delete", defaults={"perfil":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Perfil:delete.html.twig")
     */
    public function deleteAction(Request $request,Perfil $perfil)
    {
        $form          = $this->createDeleteForm($perfil);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            if(($perfil->getRoles()->count() > 0)||($perfil->getUsuarios()->count() > 0))
            {
                return new JsonResponse(array('resultado' => 2, 'mensaje' => 'No pudo ser eliminado el Perfil porque tiene Roles y/o Usuarios asignados'));
            }
            
            $em  = $this->getDoctrine()->getManager();
            $em->remove($perfil);
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Rol eliminado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'No pudo ser eliminado el Rol'));
            }
        }
        return array(
            'entity' => $perfil,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a Email entity.
     *
     * @param Perfil $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Perfil $entity)
    {
        $form = $this->createForm(PerfilType::class, $entity);
    
        return $form;
    }
    
    /**
     * Creates a form to delete a perfil entity.
     *
     * @param $perfil The perfil entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Perfil $perfil)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('perfil_delete', array('id' => $perfil->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}