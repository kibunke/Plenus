<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Disciplina;
use ResultadoBundle\Form\DisciplinaType;
/**
 * Default controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DisciplinaController extends Controller
{
    /**
     * Lists all Disciplina entities.
     *
     * @Route("/disciplina", name="disciplina")
     * @Method("GET")
     * @Template("GestionBundle:Disciplina:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ResultadoBundle:Disciplina')->findAll();
        //$aux=$em->getRepository('ResultadoBundle:Disciplina')->getOnlyRoot();
        $tree = $em->getRepository('ResultadoBundle:Disciplina')->getArbolAsArray("All");
        
        return array(
            'entities' => $entities,
            'tree' => json_encode($tree),
        );
    }
    
    /**
     * Creates a new Disciplina entity.
     *
     * @Route("/disciplina", name="disciplina_create")
     * @Method("POST")
     * @Template("GestionBundle:Disciplina:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Disciplina();
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
                return $this->redirectToRoute('disciplina');
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
     * Creates a form to create a Disciplina entity.
     *
     * @param Disciplina $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Disciplina $entity)
    {
        $form = $this->createForm(new DisciplinaType(), $entity, array(
            'action' => $this->generateUrl('disciplina_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Disciplina entity.
     *
     * @Route("/disciplina/new", name="disciplina_new")
     * @Method("GET")
     * @Template("GestionBundle:Disciplina:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Disciplina();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Disciplina entity.
     *
     * @Route("/disciplina/{id}", name="disciplina_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
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
     * Displays a form to edit an existing Disciplina entity.
     *
     * @Route("/disciplina/{id}/edit", name="disciplina_edit")
     * @Method("GET")
     * @Template("GestionBundle:Disciplina:edit.html.twig")
     */
    public function editAction(Disciplina $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Disciplina que quiere modificar.');
        }

        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Disciplina entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Disciplina $entity)
    {
        $form = $this->createForm(new DisciplinaType(), $entity, array(
            'action' => $this->generateUrl('disciplina_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Disciplina entity.
     *
     * @Route("/disciplina/{id}", name="disciplina_update")
     * @Method("PUT")
     * @Template("GestionBundle:Disciplina:edit.html.twig")
     */
    public function updateAction(Request $request,Disciplina $entity)
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
                return $this->redirectToRoute('disciplina');
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
