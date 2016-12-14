<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TesoreriaBundle\Entity\DatosTesoreria;
use TesoreriaBundle\Form\DatosTesoreriaType;
use CommonBundle\PDFs\DocumentoPDF;
use Symfony\Component\HttpFoundation\Response;

/**
 * DatosTesoreria controller.
 *
 * @Route("/datostesoreria")
 */
class DatosTesoreriaController extends Controller
{
    /**
     * Creates a new DatosTesoreria entity.
     *
     * @Route("/", name="datostesoreria_create")
     * @Method("POST")
     * @Template("TesoreriaBundle:DatosTesoreria:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new DatosTesoreria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        
        if ($form->isValid()) {
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->getUser());
            $session = $request->getSession();
            $id = $session->get('idPersonalEdit');
            $personal = $em->getRepository('AcreditacionBundle:PersonalJuegos')->find($id);
            $personal->setDatosTesoreria($entity);
            $entity->setPersonalJuegos($personal);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($personal);
            try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                // return $this->redirectToRoute('personaljuegos_edit', array('id' => $entity->getId()));
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
           // return $this->redirect($this->generateUrl('datostesoreria_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    
    
    /**
     * Creates a form to create a DatosTesoreria entity.
     *
     * @param DatosTesoreria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DatosTesoreria $entity)
    {
        $form = $this->createForm(new DatosTesoreriaType(), $entity, array(
            'action' => $this->generateUrl('datostesoreria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new DatosTesoreria entity.
     *
     * @Route("/new/{id}", name="datostesoreria_new")
     * @Method("GET")
     * @Template("TesoreriaBundle:DatosTesoreria:index.html.twig")
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $personal = $em->getRepository('AcreditacionBundle:PersonalJuegos')->find($id);
        $entity = new DatosTesoreria();
        
        //$entity->setPersonalJuegos($personal);
        $personal->setDatosTesoreria($entity);
        
        $form   = $this->createCreateForm($entity);

        $session = $request->getSession();
        $session->set('idPersonalEdit', $id);   
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a DatosTesoreria entity.
     *
     * @Route("/{id}", name="datostesoreria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DatosTesoreria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DatosTesoreria entity.
     *
     * @Route("/{id}/edit", name="datostesoreria_edit")
     * @Method("GET")
     * @Template("TesoreriaBundle:DatosTesoreria:index.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DatosTesoreria entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a DatosTesoreria entity.
    *
    * @param DatosTesoreria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DatosTesoreria $entity)
    {
        $form = $this->createForm(new DatosTesoreriaType(), $entity, array(
            'action' => $this->generateUrl('datostesoreria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing DatosTesoreria entity.
     *
     * @Route("/{id}", name="datostesoreria_update")
     * @Method("PUT")
     * @Template("TesoreriaBundle:DatosTesoreria:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DatosTesoreria entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('exito', 'La información fue guardada correctamente.');
                
            return $this->redirect($this->generateUrl('datostesoreria_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
    /**
     * Deletes a DatosTesoreria entity.
     *
     * @Route("/{id}", name="datostesoreria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DatosTesoreria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('datostesoreria'));
    }

    /**
     * Creates a form to delete a DatosTesoreria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('datostesoreria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }     
}
