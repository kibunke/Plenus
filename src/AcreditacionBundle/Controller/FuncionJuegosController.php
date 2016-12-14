<?php

namespace AcreditacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AcreditacionBundle\Entity\FuncionJuegos;
use AcreditacionBundle\Form\FuncionJuegosType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * FuncionJuegos controller.
 *
 * @Route("/gestion/funcionjuegos")
 */
class FuncionJuegosController extends Controller {

    /**
     * Lists all FuncionJuegos entities.
     *
     * @Route("/", name="funcionjuegos")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcreditacionBundle:FuncionJuegos')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new FuncionJuegos entity.
     *
     * @Route("/", name="funcionjuegos_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:FuncionJuegos:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FuncionJuegos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->getUser());
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
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FuncionJuegos entity.
     *
     * @param FuncionJuegos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FuncionJuegos $entity) {
        $form = $this->createForm(new FuncionJuegosType(), $entity, array(
            'action' => $this->generateUrl('funcionjuegos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new FuncionJuegos entity.
     *
     * @Route("/new", name="funcionjuegos_new")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:FuncionJuegos:new.html.twig")
     */
    public function newAction() {
        $entity = new FuncionJuegos();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a FuncionJuegos entity.
     *
     * @Route("/{id}", name="funcionjuegos_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:FuncionJuegos:show.html.twig")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:FuncionJuegos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FuncionJuegos entity.');
        }

        $form = $this->createForm(new FuncionJuegosType(), $entity, array(
            'action' => $this->generateUrl('funcionjuegos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FuncionJuegos entity.
     *
     * @Route("/{id}/edit", name="funcionjuegos_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:FuncionJuegos:edit.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:FuncionJuegos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FuncionJuegos entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a FuncionJuegos entity.
     *
     * @param FuncionJuegos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FuncionJuegos $entity) {
        $form = $this->createForm(new FuncionJuegosType(), $entity, array(
            'action' => $this->generateUrl('funcionjuegos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }

    /**
     * Edits an existing FuncionJuegos entity.
     *
     * @Route("/{id}", name="funcionjuegos_update")
     * @Method("PUT")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:FuncionJuegos:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcreditacionBundle:FuncionJuegos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FuncionJuegos entity.');
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
     * Deletes a FuncionJuegos entity.
     *
     * @Route("/delete", name="funcionjuegos_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request) {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("AcreditacionBundle:FuncionJuegos")->find($id);
        $result['error'] = false;
        if (!$entity) {
            $result['error'] = true;
            $this->addFlash('error', 'No se encontró la Función a eliminar.');
            return new JsonResponse($result);
        }
        if (!$em->getRepository("AcreditacionBundle:FuncionJuegos")->esEliminable($id)) {
            $result['error'] = true;
            $this->addFlash('error', 'No es posible eliminar la Función por encontrarse en uso.');
            return new JsonResponse($result);
        }
        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $e) {
            $result['error'] = true;
            $this->addFlash('error', $e->getMessage());
            return new JsonResponse($result);
        }
        $this->addFlash('exito', 'La Función fue eliminada correctamente.');
        return new JsonResponse($result);
    }

    /**
     * Lista todas las funciones de un area en particular
     * 
     * @Route("/funciones", name="funcionjuegos_funciones")
     * @Method("POST")
     * @Security("has_role('ROLE_ACREDITACION')")
     */
    public function funcionesAction(Request $request) {
        $idArea = $request->request->get('idArea');
        $em = $this->getDoctrine()->getManager();
        $funciones = $em->getRepository('AcreditacionBundle:FuncionJuegos')->getFuncionAreaId($idArea);
        $html = '<option selected="selected" value="">Seleccionar</option>';
        foreach ($funciones as $funcion) {
            $html.= '<option value="' . $funcion->getId() . '">' . $funcion->getNombre() . '</option>';
        }
        return new Response($html);
    }

}
