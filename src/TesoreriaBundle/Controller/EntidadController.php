<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use TesoreriaBundle\Form\EntidadType;
use TesoreriaBundle\Entity\Entidad;

/**
 * Entidad controller.
 *
 * @Route("/tesoreria/entidad")
 * @Security("has_role('ROLE_TESORERIA_ENTIDAD')")
 */
class EntidadController extends Controller
{
    /**
     * Lists all Entidad entities.
     *
     * @Route("/list", name="tesoreria_entidad_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:Entidad')->findAll();
        return array(
            'entidades' => $entities,
        );
    }  
    
    /**
     * Creates a new Entidad entity.
     *
     * @Route("/create", name="tesoreria_entidad_create")
     * @Method("POST")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_NEW')")
     * @Template("TesoreriaBundle:Entidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Entidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());            
            try{
                $em->persist($entity);
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                return new JsonResponse(array('success' => false));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Entidad entity.
     *
     * @param Entidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Entidad $entity)
    {
        $form = $this->createForm(new EntidadType(), $entity, array(
            'action' => $this->generateUrl('tesoreria_entidad_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Entidad entity.
     *
     * @Route("/new", name="tesoreria_entidad_new")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_NEW')")
     * @Template()
     */    
    public function newAction()
    {
        $entity = new Entidad();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Entidad entity.
     *
     * @Route("/{id}/edit", name="tesoreria_entidad_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_EDIT')")
     * @Template()
     */
    public function editAction(Entidad $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe el Fondo que quiere modificar.');
        }
        
        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Entidad entity.
    *
    * @param Entidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Entidad $entity)
    {
        $form = $this->createForm(new EntidadType(), $entity, array(
            'action' => $this->generateUrl('tesoreria_entidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    
    /**
     * Edits an existing Entidad entity.
     *
     * @Route("/{id}/update", name="tesoreria_entidad_update")
     * @Method("PUT")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_EDIT')")
     * @Template("TesoreriaBundle:Entidad:edit.html.twig")
     */
    public function updateAction(Request $request,Entidad $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        //if (!$entity->editCheck()){
        //    return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'Para poder editar el Fondo, el monto NO puede ser inferior a la suma de monto utilizado más monto reservado.'));
        //}
        
        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
                //return $this->redirectToRoute('categoria');
            } catch (Exception $e) {
                return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Entidad entity.
     *
     * @Route("/{id}/remove", name="tesoreria_entidad_remove")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Entidad $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Entidad entity.');
            }

            $em->remove($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'La Entidad fue eliminado con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'Ocurrio un error al intentar eliminar la Entidad.');
            }
        }
        return $this->redirectToRoute('tesoreria_entidad_list');
    }

    /**
     * Creates a form to delete a Entidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Entidad $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tesoreria_entidad_remove', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Entidad entity.
     *
     * @Route("/{id}/delete", name="tesoreria_entidad_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_ENTIDAD_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Entidad $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('tesoreria_entidad_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Entidad.');
        }
        
        if (!$entity->deleteCheck()){
            return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'Para poder borrar la Entidad no debe tener fondos asociados.'));
        }
        
        $form = $this->createDeleteForm($entity);
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }    
}
