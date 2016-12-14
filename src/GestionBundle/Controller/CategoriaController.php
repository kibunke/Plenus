<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Categoria;
use GestionBundle\Form\CategoriaType;
/**
 * Categoria controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CategoriaController extends Controller
{
    /**
     * Lists all Categoria entities.
     *
     * @Route("/categoria", name="categoria")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * Creates a new Categoria entity.
     *
     * @Route("/categoria", name="categoria_create")
     * @Method("POST")
     * @Template("GestionBundle:Categoria:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Categoria();
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
                return $this->redirectToRoute('categoria');
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
     * Creates a form to create a Categoria entity.
     *
     * @param Categoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('categoria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Categoria entity.
     *
     * @Route("/categoria/new", name="categoria_new")
     * @Method("GET")
     * @Template("GestionBundle:Categoria:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Categoria();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Categoria entity.
     *
     * @Route("/categoria/{id}", name="categoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Categoria $id)
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
     * Displays a form to edit an existing Categoria entity.
     *
     * @Route("/categoria/{id}/edit", name="categoria_edit")
     * @Method("GET")
     * @Template("GestionBundle:Categoria:edit.html.twig")
     */
    public function editAction(Categoria $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Categoria que quiere modificar.');
        }

        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Categoria entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('categoria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Categoria entity.
     *
     * @Route("/categoria/{id}", name="categoria_update")
     * @Method("PUT")
     * @Template("GestionBundle:Categoria:new.html.twig")
     */
    public function updateAction(Request $request,Categoria $entity)
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
                return $this->redirectToRoute('categoria');
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
