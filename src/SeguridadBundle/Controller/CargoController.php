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

use SeguridadBundle\Form\CargoType;
use SeguridadBundle\Entity\Cargo;

/**
 * Cargo controller.
 *
 * @Route("/system/cargo")
 */
class CargoController extends Controller
{
    /**
     * @Route("/list", name="cargo_list")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="cargo_list_datatable")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listDataTableAction(Request $request)
    {
        $user   = $this->getUser();
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('SeguridadBundle:Cargo')->datatable($request->request);
        $cargos = [];
        foreach($filter['rows'] as $cargo)
        {
            $cargos[] = array( 'id'          => $cargo->getId(),
                               'name'        => $cargo->getName(),
                               'description' => $cargo->getDescription(),
                               'isActive'    => $cargo->getIsActive(),
                               'perfiles'    => $cargo->getPerfiles()->count(),
                               'usuarios'    => $cargo->getUsuarios()->count(),
                             );
        }
        $data=array(
            "draw"=> $request->request->get('draw'),
            "recordsTotal"=> $filter['total'],
            "recordsFiltered"=> $filter['filtered'],
            "data"=> $cargos,
        );
        
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/{cargo}/edit", name="cargo_edit", defaults={"cargo":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Cargo:new.html.twig")
     */
    public function editAction(Request $request,Cargo $cargo)
    {
        $em       = $this->getDoctrine()->getManager();
        $perfiles = $em->getRepository('SeguridadBundle:Perfil')->findBy(array('isActive'=>true));
        $form     = $this->createEditForm($cargo);
        $form->handleRequest($request);
     
        if ($form->isSubmitted() && $form->isValid())
        {
            $cargo->setUpdatedBy($this->getUser());
            try{
                $em->flush();
                $this->addFlash('success', "Cargo ". $cargo->getName() . " modificado con éxito");
                return $this->redirectToRoute('cargo_edit',array('cargo' => $cargo->getId()));
            }catch(\Exception $e ){
                $this->addFlash('error', "El Cargo no pudo ser modificado");
                return $this->redirectToRoute('cargo_edit',array('cargo' => $cargo->getId()));
            }
        }
        return array(
            'entity'   => $cargo,
            'form'     => $form->createView(),
            'perfiles' => $perfiles
        );
    }
    
    /**
     * @Route("/new", name="cargo_new")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Cargo:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $entity   = new Cargo();
        $form     = $this->createCreateForm($entity);
        $em       = $this->getDoctrine()->getManager();
        $perfiles = $em->getRepository('SeguridadBundle:Perfil')->findBy(array('isActive'=>true));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                $this->addFlash('success', "Cargo ". $entity->getName() . " creado con éxito");
                return $this->redirectToRoute('cargo_list');
            }
            catch(\Exception $e ){
                $this->addFlash('error', "El Cargo no pudo ser creado");
                return $this->redirectToRoute('cargo_list');
            }
        }
        return array(
            'entity'   => $entity,
            'form'     => $form->createView(),
            'perfiles' => $perfiles
        );
    }

    /**
     * @Route("/{cargo}/delete", name="cargo_delete", defaults={"cargo":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("SeguridadBundle:Cargo:delete.html.twig")
     */
    public function deleteAction(Request $request,Cargo $cargo)
    {
        $form = $this->createDeleteForm($cargo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            if($cargo->getPerfiles()->count() > 0)
            {
                return new JsonResponse(array('resultado' => 2, 'mensaje' => 'No pudo ser eliminado el Cargo porque tiene Roles y/o Usuarios asignados'));
            }
            
            $em  = $this->getDoctrine()->getManager();
            $em->remove($cargo);
  
            try{
                $em->flush();
                return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Cargo eliminado con éxito'));
            }
            catch(\Exception $e ){
                 return new JsonResponse(array('resultado' => 1, 'mensaje' => 'No pudo ser eliminado el Rol'));
            }
        }
        return array(
            'entity' => $cargo,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a Email entity.
     *
     * @param Cargo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cargo $entity)
    {
        $form = $this->createForm(CargoType::class, $entity);
    
        return $form;
    }
    
     /**
     * Creates a form to create a Email entity.
     *
     * @param Cargo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cargo $entity)
    {
        $form = $this->createForm(CargoType::class, $entity,
                                  array(
                                        'action' => $this->generateUrl('cargo_edit', array('cargo' => $entity->getId())),
                                        'method' => 'POST'
                                       )
                                  );
    
        return $form;
    }
    
    /**
     * Creates a form to delete a perfil entity.
     *
     * @param $cargo The perfil entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cargo $cargo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cargo_delete', array('id' => $cargo->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}