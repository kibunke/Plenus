<?php

namespace AcreditacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AcreditacionBundle\Entity\Area;
use AcreditacionBundle\Form\AreaType;
use AcreditacionBundle\Entity\AreaCategoriaPago;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Area controller.
 *
 * @Route("/gestion/area")
 */
class AreaController extends Controller {

    /**
     * Lists all Area entities.
     *
     * @Route("/", name="area")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcreditacionBundle:Area')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Area entity.
     *
     * @Route("/", name="area_create")
     * @Method("POST")
     * @Template("AcreditacionBundle:Area:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request) {
        $entity = new Area();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->get('submit')->isClicked()) {
            if (!$form->isValid()) {
                $this->addFlash('error', 'Los datos ingresados no son válidos.');
                return array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                );
            }
            if (!$this->validateArea($entity)) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                return array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                );
            }

            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente. ' . $e->getMessage());
            }
            return $this->redirect($this->generateUrl('acreditacion_parameters'));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Area entity.
     *
     * @param Area $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Area $entity) {
        $form = $this->createForm(new AreaType(), $entity, array(
            'action' => $this->generateUrl('area_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Area entity.
     *
     * @Route("/new", name="area_new")
     * @Method("GET")
     * @Template("AcreditacionBundle:Area:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = new Area();
        $categorias = $em->getRepository('TesoreriaBundle:CategoriaPago')->getAllOrdered();
        foreach ($categorias as $categoria) {
            $ACPago = new AreaCategoriaPago();
            $ACPago->setCategoria($categoria);
            $entity->addCuposCategoriasPago($ACPago);
        }
        $form = $this->createCreateForm($entity);
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Area entity.
     *
     * @Route("/{id}", name="area_show")
     * @Method("GET")
     * @Template("AcreditacionBundle:Area:show.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $form = $this->createForm(new AreaType(), $entity, array(
            'action' => $this->generateUrl('area_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Area entity.
     *
     * @Route("/{id}/edit", name="area_edit")
     * @Method("GET")
     * @Template("AcreditacionBundle:Area:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:Area')->findOneById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Area entity.
     *
     * @param Area $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Area $entity) {
        $form = $this->createForm(new AreaType(), $entity, array(
            'action' => $this->generateUrl('area_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }

    /**
     * 
     */
    private function validateArea(Area $area) {
        $totalCP = 0;
        foreach ($area->getCuposCategoriasPago() as $categoria) {
            $totalCP += $categoria->getCupoMax();
        }
        if ($totalCP > $area->getCupoMaxPersonal()) {
            $this->addFlash('error', 'La suma de los cupos de Categorías de Pago ha sobrepasado el Máximo de Acreditación del personal.');
            return false;
        }
        if ($area->getCupoMaxTransporte() > $area->getCupoMaxPersonal()) {
            $this->addFlash('error', 'El Cupo Máximo de Transporte ha sobrepasado el Máximo de Acreditación del personal.');
            return false;
        }
        if ($area->getCupoMaxHoteleria() > $area->getCupoMaxPersonal()) {
            $this->addFlash('error', 'El Cupo Máximo de Hospedaje ha sobrepasado el Máximo de Acreditación del personal.');
            return false;
        }
        return true;
    }

    /**
     * Edits an existing Area entity.
     *
     * @Route("/{id}", name="area_update")
     * @Method("PUT")
     * @Template("AcreditacionBundle:Area:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->get('submit')->isClicked()) {
            if (!$editForm->isValid()) {
                $this->addFlash('error', 'Los datos ingresados no son válidos.');
                return array(
                    'entity' => $entity,
                    'form' => $editForm->createView(),
                );
            }
            if (!$this->validateArea($entity)) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                return array(
                    'entity' => $entity,
                    'form' => $editForm->createView(),
                );
            }
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->getUser());
            $em->persist($entity);
            try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
            } catch (\Exception $e) {
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
     * Deletes a Area entity.
     *
     * @Route("/delete", name="area_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request) {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("AcreditacionBundle:Area")->find($id);
        if (!$entity) {
            $this->addFlash('error', 'No se encontró el Área a eliminar.');
            return new JsonResponse(array('error' => true));
        }
        if (!$em->getRepository("AcreditacionBundle:Area")->esEliminable($id)) {
            $this->addFlash('error', 'No es posible eliminar el Área por encontrarse en uso.');
            return new JsonResponse(array('error' => true));
        }
        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return new JsonResponse(array('error' => true));
        }
        $this->addFlash('exito', 'El Área fue eliminada correctamente.');
        return new JsonResponse(array('error' => false));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeCategoriaPago($idCatPag, $em = null) {
        $em = ($em) ? $em : $this->getDoctrine()->getManager();
        $catPago = $em->getRepository('AcreditacionBundle:AreaCategoriaPago')->find($idCatPag);
        if (!$catPago) {
            return false;
        }
        $areas = $em->getRepository('AcreditacionBundle:Area')->findAll();
        foreach ($areas as $area) {
            $area->removeCuposCategoriasPago($catPago);
            $em->flush();
        }
        return true;
    }

    /**
     *
     * 
     * @Route("/categorias", name="area_categorias")
     * @Method("POST")
     * @Template("AcreditacionBundle:Area:categoriasPago.html.twig")
     */
    public function getCategoriasAction(Request $request) {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        try {
            $area = $em->getRepository("AcreditacionBundle:Area")->find($id);
            $i = 1;
            $result = array();
            foreach ($area->getCuposCategoriasPago() as $categoria) {
                $result[$i]['nombre'] = $categoria->getCategoria()->getNombre();
                $result[$i]['cupoMax'] = $categoria->getCupoMax();
                $result[$i]['acreditados'] = count($em->getRepository("AcreditacionBundle:PersonalJuegos")->getAcreditadosCategoria($area->getId(), $categoria->getCategoria()->getNombre()));
                $i++;
            }
            return array(
                'categorias' => $result,
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'La información no pude ser obtenida.');
        }
    }

}
