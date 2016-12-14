<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use TesoreriaBundle\Entity\CategoriaPago;
use AcreditacionBundle\Entity\AreaCategoriaPago;
use TesoreriaBundle\Form\CategoriaPagoType;
use Symfony\Component\HttpFoundation\Response;

/**
 * CategoriaPago controller.
 *
 * @Route("/gestion/categoriapago")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CategoriaPagoController extends Controller {

    /**
     * Lists all CategoriaPago entities.
     *
     * @Route("/", name="categoriapago")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:CategoriaPago')->findBy(array(), array('nombre' => 'ASC'));
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new CategoriaPago entity.
     *
     * @Route("/", name="categoriapago_create")
     * @Method("POST")
     * @Template("TesoreriaBundle:CategoriaPago:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CategoriaPago();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $areas = $em->getRepository('AcreditacionBundle:Area')->findAll(); //Se actualizan todas la areas ya registradas agregando la nueva categoria registrada
            foreach ($areas as $area) {
                $ACPago = new AreaCategoriaPago();
                $ACPago->setCategoria($entity);
                $area->addCuposCategoriasPago($ACPago);
                $em->persist($area);
            } try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
            return $this->redirect($this->generateUrl('acreditacion_parameters'));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a CategoriaPago entity.
     *
     * @param CategoriaPago $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CategoriaPago $entity) {
        $form = $this->createForm(new CategoriaPagoType(), $entity, array(
            'action' => $this->generateUrl('categoriapago_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Crear'));
        return $form;
    }

    /**
     * Displays a form to create a new CategoriaPago entity.
     *
     * @Route("/new", name="categoriapago_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CategoriaPago();
        $form = $this->createCreateForm($entity);
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a CategoriaPago entity.
     *
     * @Route("/{id}", name="categoriapago_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TesoreriaBundle:CategoriaPago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPago entity.');
        }
        $form = $this->createForm(new CategoriaPagoType(), $entity);
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CategoriaPago entity.
     *
     * @Route("/{id}/edit", name="categoriapago_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TesoreriaBundle:CategoriaPago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPago entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a CategoriaPago entity.
     *
     * @param CategoriaPago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CategoriaPago $entity) {
        $form = $this->createForm(new CategoriaPagoType(), $entity, array(
            'action' => $this->generateUrl('categoriapago_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Actualizar'));
        return $form;
    }

    /**
     * Edits an existing CategoriaPago entity.
     *
     * @Route("/{id}", name="categoriapago_update")
     * @Method("PUT")
     * @Template("TesoreriaBundle:CategoriaPago:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TesoreriaBundle:CategoriaPago')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPago entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->getUser());
            $em->persist($entity);
            try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
            return $this->redirect($this->generateUrl('acreditacion_parameters'));
        }
        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a CategoriaPago entity.
     *
     * @Route("/delete", name="categoriapago_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("TesoreriaBundle:CategoriaPago")->find($id);
        $error = 'true';
        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $error = 'false';
            } catch (Exception $e) {
                $error = 'true';
            }
        }
        return new Response($error);
    }

    /**
     * Creates a form to delete a CategoriaPago entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('categoriapago_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }
    
    
    /**
     * Obiene el monto de pago de una categoria en particular recibida como parámetro
     * 
     * @Route("/{id}/getMonto", name="categoriapago_getMonto", defaults={"id" = "0" })
     * @Method({"GET", "POST"})
     */
    public function getMontoAction(Request $request, $id) {
        if ($request->getMethod() == 'POST') {
            $id = $request->request->get('id');
        }
        $em = $this->getDoctrine()->getManager();
        try {
            $categoriaEntity = $em->getRepository('TesoreriaBundle:CategoriaPago')->find($id);
            return new Response($categoriaEntity->getMonto());
        } catch (\Exception $e) {
            $this->addFlash('error', 'La información no pude ser obtenida.');
        }
    }

}
