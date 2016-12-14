<?php

namespace ResultadoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\Plaza;
use ResultadoBundle\Entity\PlazaZona;
use ResultadoBundle\Entity\PlazaSerie;
use ResultadoBundle\Entity\PlazaCopa;
use ResultadoBundle\Entity\Zona;
use ResultadoBundle\Entity\Serie;
use ResultadoBundle\Form\PlazaType;

/**
 * Plaza controller.
 *
 * @Route("/resultados/plaza")
 * @Security("has_role('ROLE_PLAZA')")
 */
class PlazaController extends Controller
{
    /**
     * Creates a new Plaza entity.
     *
     * @Route("/{id}/create", name="plaza_create")
     * @Method("POST")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function createAction(Request $request, Competencia $competencia)
    {
        $entity = new PlazaZona($this->getUser());
        $entity->setCompetencia($competencia);    
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Plaza entity.
     *
     * @Route("/{zona}/create/zona", name="plaza_zona_create")
     * @Method("POST")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function createZonaAction(Request $request, Zona $zona)
    {
        $entity = new PlazaZona($this->getUser());
        $entity->setZona($zona);
        $entity->setCompetencia($zona->getLiga());    
        $form = $this->createCreateZonaForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a new Plaza entity.
     *
     * @Route("/{serie}/create/serie", name="plaza_serie_create")
     * @Method("POST")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function createSerieAction(Request $request, Serie $serie)
    {
        $entity = new PlazaSerie($this->getUser(),$serie->getCompetencia());
        $entity->setSerie($serie);
        $entity->setCompetencia($serie->getCompetencia());    
        $form = $this->createCreateSerieForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a Plaza entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Plaza $entity)
    {
        $form = $this->createForm($entity->getCompetencia()->getPlazaType(), $entity, array(
            'action' => $this->generateUrl('plaza_create', array('id' => $entity->getCompetencia()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                            )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Creates a form to create a Plaza entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateZonaForm(Plaza $entity)
    {
        $form = $this->createForm($entity->getCompetencia()->getPlazaType(), $entity, array(
            'action' => $this->generateUrl('plaza_zona_create', array('zona' => $entity->getZona()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                            )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
    /**
     * Creates a form to create a Plaza entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateSerieForm(Plaza $entity)
    {
        $form = $this->createForm($entity->getCompetencia()->getPlazaType(), $entity, array(
            'action' => $this->generateUrl('plaza_serie_create', array('serie' => $entity->getSerie()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                            )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
    /**
     * Displays a form to create a new Plaza entity.
     *
     * @Route("/{id}/new", name="plaza_new")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function newAction(Request $request, Competencia $competencia)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Plaza();
        $entity->setCompetencia($competencia);
        $entity->setNombre("Equipo ".(count($competencia->getPlazas())+1));

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Plaza entity.
     *
     * @Route("/{zona}/new/zona", name="plaza_zona_new")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function newZonaAction(Request $request, Zona $zona )
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new PlazaZona();
        $entity->setCompetencia($zona->getLiga());
        $entity->setZona($zona);
        $entity->setNombre("Equipo ".(count($zona->getPlazas())+1)." - ".$zona);

        $form   = $this->createCreateZonaForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new Plaza entity.
     *
     * @Route("/{serie}/new/serie", name="plaza_serie_new")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_NEW')")
     * @Template("ResultadoBundle:Plaza:new.html.twig")
     */
    public function newSerieAction(Request $request, Serie $serie)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new PlazaSerie($this->getUser(),$serie->getCompetencia());
        $entity->setCompetencia($serie->getCompetencia());
        $entity->setSerie($serie);
        $entity->setNombre("Equipo ".(count($serie->getPlazas())+1)." - ".$serie);

        $form   = $this->createCreateSerieForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Plaza entity.
     *
     * @Route("/{id}", name="plaza_show")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_SHOW')")
     * @Template()
     */
    public function showAction(Request $request, Plaza $entity)
    {
        //if (!$request->isXMLHttpRequest()){
        //    return $this->redirectToRoute('inscripcion');
        //}
        ///* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        //if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
        //    $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
        //    return new JsonResponse(array('success' => false, 'reload' =>true));        
        //}
        //
        //$em = $this->getDoctrine()->getManager();
        //
        //if (!$entity) {
        //    throw $this->createNotFoundException('Unable to find Plaza entity.');
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
     * Displays a form to edit an existing Plaza entity.
     *
     * @Route("/{id}/edit", name="plaza_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Plaza $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Plaza.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Plaza entity.
    *
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Plaza $entity)
    {
        $form = $this->createForm($entity->getCompetencia()->getPlazaType(), $entity, array(
            'action' => $this->generateUrl('plaza_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Plaza entity.
     *
     * @Route("/{id}", name="plaza_update")
     * @Method("POST")
     * @Security("has_role('ROLE_PLAZA_EDIT')")
     * @Template()
     */
    public function updateAction(Request $request, Plaza $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Plaza.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
    








 











    
    /**
     * Deletes a Plaza entity.
     *
     * @Route("/{id}/remove", name="plaza_delete_flush")
     * @Security("has_role('ROLE_PLAZA_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Plaza $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                $this->addFlash('primary', 'No existe la Plaza.');
                return new JsonResponse(array('success' => false, 'reload' =>true));
            }

            $em->remove($entity);
            try{
                $em->flush();
                //$this->addFlash('exito', 'La planilla fue eliminada con exito ');
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser eliminada.'));
            }
        }else{
            $this->addFlash('primary', 'Imposible eliminar la Plaza. La información no es válida.');
        }
        return new JsonResponse(array('success' => false, 'reload' =>true));
    }

    /**
     * Creates a form to delete a Plaza entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Plaza $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('plaza_delete_flush', array('id' => $entity->getId())),
                'method' => 'DELETE',
                'attr' => array(
                                'data-idreload' => "reload-parcial-".$entity->getIdReload()
                        )
                )
            )
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}/remove", name="plaza_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Plaza $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            $this->addFlash('primary', 'No existe la Plaza.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        $form = $this->createDeleteForm($entity);
        
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
    
    /**
     * Update posiciones de plazas en una competencia.
     *
     * @Route("/{id}/posicion/update", name="plaza_posicion_update")
     * @Method("POST")
     * @Security("has_role('ROLE_PLAZA_POSICION_UPDATE')")
     * @Template()
     */
    public function posicionUpdateAction(Request $request, Competencia $competencia)
    {
        $asd=$request->request->get('ids');
        $ids = $request->request->get('ids');
        $isReset = $request->request->get('isReset')=='false'?false:true;//reseteo true o false
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($competencia->getEtapa()->getEvento())){
            return new JsonResponse(array('success' => false, 'msj' => 'No puede ver información de un evento que no coordina.'));
        }
        
        /* CHEQUEA QUE EXISTA LA COMPETENCIA */
        if(!$competencia){
            return new JsonResponse(array('success' => false, 'msj' => 'No existe la competencia que quiere modificar.'));
        }
        
        $ids = explode(",",$ids);
        if (count($ids)>0){
            foreach ($competencia->getPlazas() as $plaza)
            {
                $aux = array_search($plaza->getId(),$ids);
                if ($aux || $aux == 0){
                    if ($isReset){
                        $plaza->setOrden(99);
                    }else{
                        $plaza->setOrden($aux+1);
                    }
                }
            }
            $em = $this->getDoctrine()->getManager();
    
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'reload' => true));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La operación no puedo completarse. Persist exception error.'));
            }
        }
        return new JsonResponse(array('success' => false, 'msj' => 'Los datos no son válidos. Contacte al administrador.'));
    }
}
