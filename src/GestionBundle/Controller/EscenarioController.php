<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Escenario;
use ResultadoBundle\Form\EscenarioType;

/**
 * Escenario controller.
 *
 * @Route("/gestion/escenario")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EscenarioController extends Controller
{
    /**
     * Lists all Escenario entities.
     *
     * @Route("/escenario", name="escenario")
     * @Method("GET")
     * @Template("GestionBundle:Escenario:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ResultadoBundle:Escenario')->findAll();
        
        return array(
            'entities' => $entities
        );
    }
    
    /**
     * Creates a new Evento entity.
     *
     * @Route("/new", name="escenario_create")
     * @Method("POST")
     * @Template("GestionBundle:Escenario:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Escenario($this->getUser());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());            
            try{
                $em->persist($entity);
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return $this->redirectToRoute('escenario');
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
     * Creates a form to create a Evento entity.
     *
     * @param Evento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Escenario $entity)
    {
        $form = $this->createForm(new EscenarioType(), $entity, array(
            'action' => $this->generateUrl('escenario_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Escenario entity.
     *
     * @Route("/new", name="escenario_new")
     * @Method("GET")
     * @Template("GestionBundle:Escenario:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Escenario($this->getUser());
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Escenario entity.
     *
     * @Route("/{id}/show", name="escenario_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Escenario $id)
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
     * Displays a form to edit an existing Evento entity.
     *
     * @Route("/{id}/edit", name="escenario_edit")
     * @Method("GET")
     * @Template("GestionBundle:Escenario:edit.html.twig")
     */
    public function editAction(Escenario $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Escenario que quiere modificar.');
        }

        $form = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Escenario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Escenario $entity)
    {
        $form = $this->createForm(new EscenarioType(), $entity, array(
            'action' => $this->generateUrl('escenario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Escenario entity.
     *
     * @Route("/{id}", name="escenario_update")
     * @Method("PUT")
     * @Template("GestionBundle:Escenario:new.html.twig")
     */
    public function updateAction(Request $request, Escenario $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Escenario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return $this->redirectToRoute('escenario');
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
