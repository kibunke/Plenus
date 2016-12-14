<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Zona;
use ResultadoBundle\Form\ZonaType;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaLiga;

/**
 * Zona controller.
 *
 * @Route("/resultados/etapa/zona")
 * @Security("has_role('ROLE_ZONA')")
 */
class ZonaController extends Controller
{
    private $letras = ["A","B","C","D","E","F","G","H","I","J"];
    
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/plazas", name="zona_reload")
     * @Method("GET")
     * @Security("has_role('ROLE_ZONA_SHOW')")
     */
    public function showDetalleAction(Request $request, Zona $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:'.$entity->getLiga()->getFolder().':zona.html.twig',
            array(
                  'competencia' => $entity->getLiga(),
                  'zona' => $entity
                )
        );
    }
    
    /**
     * Creates a new Zona entity.
     *
     * @Route("/{id}/create", name="zona_create")
     * @Method("POST")
     * @Security("has_role('ROLE_ZONA_NEW')")
     * @Template("ResultadoBundle:Zona:new.html.twig")
     */
    public function createAction(Request $request, CompetenciaLiga $liga)
    {
        $entity = new Zona($this->getUser());
        $entity->setLiga($liga);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($liga->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
     * Creates a form to create a Zona entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Zona $entity)
    {
        $form = $this->createForm(new ZonaType(), $entity, array(
            'action' => $this->generateUrl('zona_create', array('id' => $entity->getLiga()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-total-".$entity->getLiga()->getId()
                            )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Zona entity.
     *
     * @Route("/{id}/new", name="zona_new")
     * @Method("GET")
     * @Security("has_role('ROLE_ZONA_NEW')")
     * @Template()
     */
    public function newAction(Request $request, CompetenciaLiga $liga)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Zona();
        $entity->setNombre("Zona ".$this->letras[count($liga->getZonas())]);
        $entity->setLiga($liga);

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Zona entity.
     *
     * @Route("/{id}/edit", name="zona_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_ZONA_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Zona $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getLiga()->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Zona entity.
    *
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Zona $entity)
    {
        $form = $this->createForm(new ZonaType(), $entity, array(
            'action' => $this->generateUrl('zona_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-total-".$entity->getLiga()->getId()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Zona entity.
     *
     * @Route("/{id}", name="zona_update")
     * @Method("POST")
     * @Security("has_role('ROLE_ZONA_EDIT')")
     * @Template()
     */
    public function updateAction(Request $request, Zona $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Zona.');
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
     * Deletes a Zona entity.
     *
     * @Route("/{id}/remove", name="zona_delete_flush")
     * @Security("has_role('ROLE_ZONA_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Zona $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                $this->addFlash('primary', 'No existe la Zona.');
                return new JsonResponse(array('success' => false, 'reload' =>true));
            }
            foreach($entity->getPlazas() as $item){
                $em->remove($item);    
            }
            
            try{
                $em->flush();
                $em->remove($entity);
                $em->flush();
                //$this->addFlash('exito', 'La planilla fue eliminada con exito ');
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (Exception $e) {
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
    private function createDeleteForm(Zona $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('zona_delete_flush', array('id' => $entity->getId())),
                'method' => 'DELETE',
                'attr' => array(
                                'data-idreload' => "reload-total-".$entity->getLiga()->getId()
                        )
                )
            )
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}/remove", name="zona_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Zona $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getLiga()->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        $form = $this->createDeleteForm($entity);
        
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }    
}
