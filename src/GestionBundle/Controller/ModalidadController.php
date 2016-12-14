<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Modalidad;
use GestionBundle\Form\ModalidadType;
/**
 * Modalidad controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ModalidadController extends Controller
{
    /**
     * Lists all Modalidad entities.
     *
     * @Route("/modalidad", name="modalidad")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * Creates a new Modalidad entity.
     *
     * @Route("/modalidad", name="modalidad_create")
     * @Method("POST")
     * @Template("GestionBundle:Modalidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Modalidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());            
            try{
                $em->persist($entity);
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                //return new JsonResponse(array('success' => true));
                return $this->redirectToRoute('modalidad');
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');    
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Modalidad entity.
     *
     * @param Modalidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Modalidad $entity)
    {
        $form = $this->createForm(new ModalidadType(), $entity, array(
            'action' => $this->generateUrl('modalidad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Modalidad entity.
     *
     * @Route("/modalidad/new", name="modalidad_new")
     * @Method("GET")
     * @Template("GestionBundle:Modalidad:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Modalidad();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Modalidad entity.
     *
     * @Route("/modalidad/{id}", name="modalidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Modalidad $id)
    {
        //$em = $this->getDoctrine()->getManager();
        //
        //$entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);
        //
        //if (!$entity) {
        //    throw $this->createNotFoundException('Unable to find Usuario entity.');
        //}
        //
        //$deleteForm = $this->createDeleteForm($id);
        //
        //return array(
        //    'entity'      => $entity,
        //    'delete_form' => $deleteForm->createView(),
        //);
    }

    /**
     * Displays a form to edit an existing Modalidad entity.
     *
     * @Route("/modalidad/{id}/edit", name="modalidad_edit")
     * @Method("GET")
     * @Template("GestionBundle:Modalidad:edit.html.twig")
     */
    public function editAction(Modalidad $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Modalidad que quiere modificar.');
        }

        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Modalidad entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Modalidad $entity)
    {
        $form = $this->createForm(new ModalidadType(), $entity, array(
            'action' => $this->generateUrl('modalidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Modalidad entity.
     *
     * @Route("/modalidad/{id}", name="modalidad_update")
     * @Method("PUT")
     * @Template("GestionBundle:Modalidad:new.html.twig")
     */
    public function updateAction(Request $request,Modalidad $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $oldEntity = clone $entity;

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                //return new JsonResponse(array('success' => true));
                return $this->redirectToRoute('modalidad');
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');    
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }    
}
