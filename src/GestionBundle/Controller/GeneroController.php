<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Genero;
use GestionBundle\Form\GeneroType;
/**
 * Genero controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class GeneroController extends Controller
{
    /**
     * Lists all Genero entities.
     *
     * @Route("/genero", name="genero")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * Creates a new Genero entity.
     *
     * @Route("/genero", name="genero_create")
     * @Method("POST")
     * @Template("GestionBundle:Genero:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Genero();
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
                return $this->redirectToRoute('genero');
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
     * Creates a form to create a Genero entity.
     *
     * @param Genero $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Genero $entity)
    {
        $form = $this->createForm(new GeneroType(), $entity, array(
            'action' => $this->generateUrl('genero_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Genero entity.
     *
     * @Route("/genero/new", name="genero_new")
     * @Method("GET")
     * @Template("GestionBundle:Genero:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Genero();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Genero entity.
     *
     * @Route("/genero/{id}", name="genero_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Genero $id)
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
     * Displays a form to edit an existing Genero entity.
     *
     * @Route("/genero/{id}/edit", name="genero_edit")
     * @Method("GET")
     * @Template("GestionBundle:Genero:edit.html.twig")
     */
    public function editAction(Genero $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Genero que quiere modificar.');
        }

        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Genero entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Genero $entity)
    {
        $form = $this->createForm(new GeneroType(), $entity, array(
            'action' => $this->generateUrl('genero_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Genero entity.
     *
     * @Route("/genero/{id}", name="genero_update")
     * @Method("PUT")
     * @Template("GestionBundle:Genero:new.html.twig")
     */
    public function updateAction(Request $request,Genero $entity)
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
                return $this->redirectToRoute('genero');
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
