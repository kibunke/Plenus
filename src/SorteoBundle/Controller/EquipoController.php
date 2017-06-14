<?php

namespace SorteoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use ResultadoBundle\Entity\Equipo;
use ResultadoBundle\Form\EquipoType;
use ResultadoBundle\Entity\Evento;

/**
 * Default controller.
 *
 * @Route("/ganadores")
 * @Security("has_role('ROLE_SORTEO')")
 */
class EquipoController extends Controller
{
    /**
     * @Route("/{evento}/equipos", name="ganadores_evento")
     * @Method("GET")
     * @Security("has_role('ROLE_EQUIPO_SHOW')")
     * @Template()
     */
    public function indexAction(Evento $evento)
    {
        return array(
            'evento' => $evento
        );
    }

    /**
     * Finds and displays a Equipo entity.
     *
     * @Route("/{id}", name="equipo_show")
     * @Method("GET")
     * @Security("has_role('ROLE_EQUIPO_SHOW')")
     * @Template()
     */
    public function showAction(Request $request, Equipo $entity)
    {

    }

    /**
     * Creates a new Equipo entity.
     *
     * @Route("/equipo/{id}/new", name="equipo_create")
     * @Method("POST")
     * @Security("has_role('ROLE_EQUIPO_NEW')")
     * @Template()
     */
    public function createAction(Request $request,Evento $evento)
    {
        $entity = new Equipo($this->getUser(),$evento);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*
             * Evita duplicados en la misma area e intercabia
             * realaciones para evitar persistir participantes duplicados
            */
            foreach ($entity->getParticipantes() as $participante) {
                $participante->setCreatedBy($this->getUser());
                $aux = $this->getDoctrine()->getRepository('ResultadoBundle:Participante')->getParticipanteByDni($participante);
                if ($aux){
                    if (!$aux->validarAsignacion($participante)){
                        return new JsonResponse(array(
                                                  'success' => false,
                                                  'dni' => $aux->getDocumentoNro(),
                                                  'msj' => 'El DNI <strong>'.$aux->getDocumentoNro().'</strong> está duplicado en el <strong>EVENTO: </strong>'.$aux->getEquipos()[0]->getEvento().'<strong> EQUIPO: </strong>'.$aux->getEquipos()[0].'.<br> Por reglamento se prohibe a los finalista participar en más de un evento de la misma Area (Deporte/Cultura).'));
                    }else{
                        $this->addFlash('primary', 'El participante con DNI <strong>'.$aux->getDocumentoNro().'</strong> ahora es finalista en este evento yes finalista en el evento <strong>'.$aux->getEquipos()[0]->getEvento().'</strong>');
                    }
                    $entity->removeParticipante($participante);
                    $entity->addParticipante($aux);
                }
            }
            $em->persist($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
            } catch (\Exception $e) {
                if (strpos($e->getMessage(),'Duplicate entry ')!== false){
                    return new JsonResponse(array('success' => false, 'msj' => 'Ocurrio un error y se intentó dar de alta un participante con DNI duplicado'));
                }
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
            return $this->redirectToRoute('ganadores_evento', array('evento' => $entity->getEvento()->getId()));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Equipo entity.
     *
     * @param Equipo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Equipo $entity)
    {
        $form = $this->createForm(new EquipoType(), $entity, array(
            'action' => $this->generateUrl('equipo_create', array('id' => $entity->getEvento()->getId())),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Equipo entity.
     *
     * @Route("/equipo/{id}/new", name="equipo_new")
     * @Method("GET")
     * @Security("has_role('ROLE_EQUIPO_NEW')")
     * @Template()
     */
    public function newAction(Request $request, Evento $evento)
    {
        if (!$request->isXMLHttpRequest()){
            $this->addFlash('primary', 'No puede acceder a esta información.');
            return $this->redirectToRoute('ganadores_evento');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Equipo(null,$evento);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Equipo entity.
     *
     * @Route("/equipo/{id}/edit", name="equipo_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_EQUIPO_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Equipo $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('sorteo_carga');
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe el Equipo.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Equipo entity.
    *
    * @param Equipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Equipo $entity)
    {
        $form = $this->createForm(new EquipoType(), $entity, array(
            'action' => $this->generateUrl('equipo_update', array('id' => $entity->getId())),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Equipo entity.
     *
     * @Route("/equipo/{id}/update", name="equipo_update")
     * @Method("POST")
     * @Security("has_role('ROLE_EQUIPO_EDIT')")
     * @Template()
     */
    public function updateAction(Request $request, Equipo $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipo entity.');
        }
        // participantes originales antes de la edición
        // necesarios para comparar con los nuevos para las bajas
        $participantes = new ArrayCollection();
        foreach ($entity->getParticipantes() as $item) {
            $participantes->add($item);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            /*
             * Evita duplicados en la misma area e intercabia
             * realaciones para evitar participantes duplicados
            */
            foreach ($entity->getParticipantes() as $participante) {
                if (!$participante->getId()){
                    $participante->setCreatedBy($this->getUser());
                }else{
                    $participante->setUpdatedAt(new \DateTime());
                    $participante->setUpdatedBy($this->getUser());
                }
                $aux = $this->getDoctrine()->getRepository('ResultadoBundle:Participante')->getParticipanteByDni($participante);
                if ($aux){
                    $eq = $aux->getEquipos()[0];
                    if (!$aux->validarAsignacion($participante)){
                        return new JsonResponse(array(
                                                  'success' => false,
                                                  'dni' => $aux->getDocumentoNro(),
                                                  'msj' => 'El DNI <strong>'.$aux->getDocumentoNro().'</strong> está duplicado en el <strong>EVENTO: </strong>'.$eq->getEvento().'<strong> EQUIPO: </strong>'.$eq.'.<br> Por reglamento se prohibe a los finalista participar en más de un evento de la misma Area (Deporte/Cultura).'));
                    }else{
                        $this->addFlash('primary', 'El participante con DNI <strong>'.$aux->getDocumentoNro().'</strong> ahora es finalista en este evento yes finalista en el evento <strong>'.$eq->getEvento().'</strong>');
                    }
                    $entity->removeParticipante($participante);
                    $entity->addParticipante($aux);
                }
            }
            /*
             * Remove the relationship between the equipo and the participante
            */
            foreach ($participantes as $participante) {
                if (false === $entity->getParticipantes()->contains($participante)) {
                    $participante->removeEquipo($entity);
                    //si no quedan más equipos asignados al participante lo elimina
                    if (count($participante->getEquipos()) == 0){
                        $em->remove($participante);
                    }
                }
            }
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
            } catch (\Exception $e) {
                if (strpos($e->getMessage(),'Duplicate entry ')!== false){
                    return new JsonResponse(array('success' => false, 'msj' => 'Ocurrio un error y se intentó dar de alta un participante con DNI duplicado'));
                }
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.','error' => $e->getMessage()));
            }
        }
        return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
    }

    /**
     * Deletes a Equipo entity.
     *
     * @Route("/equipo/{id}/remove", name="equipo_delete_flush")
     * @Security("has_role('ROLE_EQUIPO_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Equipo $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Equipo entity.');
            }
            foreach ($entity->getParticipantes() as $participante) {
                $participante->removeEquipo($entity);
                //si no quedan más equipos asignados al participante lo elimina
                if (count($participante->getEquipos()) == 0){
                    $em->remove($participante);
                }
            }
            $em->remove($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'El equipo fue eliminado con exito ');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El equipo no pudo ser eliminado. No debe estar asignado a ningúna plaza para poder ser eliminado.');
            }
        }
        return $this->redirect($this->generateUrl('ganadores_evento', array('evento' => $entity->getEvento()->getId())));
    }

    /**
     * Creates a form to delete a Equipo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Equipo $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipo_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar', 'attr' => array('class' => 'btn-danger')))
            ->getForm()
        ;
    }

    /**
     * Deletes a Actividad entity.
     *
     * @Route("/equipo/{id}/remove", name="equipo_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_EQUIPO_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Equipo $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('ganadores_evento');
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Entidad.');
        }
        $form = $this->createDeleteForm($entity);
        //return new JsonResponse(array('success' => true, 'id' => $entity->getId(),'form' => $form->createView()));
        return array(
                'entity' => $entity,
                'form' => $form->createView()
            );
    }
}
