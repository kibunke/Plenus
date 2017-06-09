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
    
    private function isValidAction($entity)
    {        
        /**
         * CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO
         * $entity puede ser una CompetenciaLiga o una Zona
         */
        if(!$this->getUser()->hasAccessAtEvento($entity->getEtapa()->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return false;
        }

        if (!$entity)
        {
            $this->addFlash('primary', 'No existe la Entidad.');
            return false;
        }
        
        return true;
    }
    
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{entity}/plazas", name="zona_reload", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Security("has_role('ROLE_ZONA_SHOW')")
     */
    public function showDetalleAction(Request $request, Zona $entity)
    {
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Zona entity.');
        }

        return $this->render('ResultadoBundle:'.$entity->getLiga()->getFolder().':zona.html.twig',array(
                                                                                                         'competencia' => $entity->getLiga(),
                                                                                                         'zona'        => $entity
                                                                                                        )
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
        return $this->createForm(new ZonaType(),
                                 $entity,
                                 array(
                                        'action' => $this->generateUrl('zona_create', array('id' => $entity->getLiga()->getId())),
                                        'method' => 'POST',
                                        'attr'   => array('data-idreload' => "reload-total-".$entity->getLiga()->getId())
                                       )
                                 );
    }

    /**
     * Displays a form to create a new Zona entity.
     *
     * @Route("/{liga}/new", name="zona_new", condition="request.isXmlHttpRequest()"))
     * @Security("has_role('ROLE_ZONA_NEW')")
     * @Template()
     */
    public function newAction(Request $request, CompetenciaLiga $liga)
    {
        if(!$this->isValidAction($liga))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }
        
        $entity = new Zona();
        $entity->setNombre("Zona ".$this->letras[count($liga->getZonas())]);
        $entity->setLiga($liga);

        $form  = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
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
     * Displays a form to edit an existing Zona entity.
     *
     * @Route("/{entity}/edit", name="zona_edit", condition="request.isXmlHttpRequest()"))
     * @Security("has_role('ROLE_ZONA_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Zona $entity)
    {
        if(!$this->isValidAction($entity))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if($editForm->isSubmitted() && $editForm->isValid())
        {
            $entity->setUpdatedBy($this->getUser());
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }
        
        return array(
                      'entity' => $entity,
                      'form'   => $editForm->createView(),
                    );
    }

    /**
     * Creates a form to edit a Zona entity.
     *
     * @param Zona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Zona $entity)
    {
        return $this->createForm( new ZonaType(),
                                  $entity,
                                  array(
                                         'action' => $this->generateUrl('zona_update', array('id' => $entity->getId())),
                                         'method' => 'POST',
                                         'attr' => array('data-idreload' => "reload-total-".$entity->getLiga()->getId())
                                       )
                                );
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
        return $this->createFormBuilder( NULL,
                                         array(
                                                'action' => $this->generateUrl('zona_delete_flush', array('id' => $entity->getId())),
                                                'method' => 'DELETE',
                                                'attr'   => array('data-idreload' => "reload-total-".$entity->getLiga()->getId())
                                               )
                                        )
                    ->getForm()
                    ;
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}/remove", name="zona_delete", condition="request.isXmlHttpRequest()"))
     * @Method("GET")
     * @Security("has_role('ROLE_PLAZA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Zona $entity)
    {
        if(!$this->isValidAction($entity))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }
        
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            foreach($entity->getPlazas() as $item)
            {
                $em->remove($item);
            }
            
            try{
                $em->flush();
                $em->remove($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser eliminada.'));
            }
        }
        
        return array(
                      'entity' => $entity,
                      'form' => $form->createView()  
                    );
    }
}
