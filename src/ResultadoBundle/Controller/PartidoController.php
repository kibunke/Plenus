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
use ResultadoBundle\Entity\Partido;
use ResultadoBundle\Entity\PartidoPuntos;
use ResultadoBundle\Form\PartidoType;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaLiga;

/**
 * Partido controller.
 *
 * @Route("/resultados/etapa/partido")
 * @Security("has_role('ROLE_PARTIDO')")
 */
class PartidoController extends Controller
{
    /**
     * @Route("/{id}/reload", name="partido_reload_view", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_SHOW')")
     */
    public function showDetalleAction(Request $request, Partido $entity)
    {
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Partido entity.');
        }
        if ($entity->getCopa())
        {
            return $this->render( 'ResultadoBundle:'.$entity->getCopa()->getFolder().':edit.html.twig',array('competencia' => $entity->getCopa()));
        }else{
            return $this->render('ResultadoBundle:'.$entity->getZona()->getLiga()->getFolder().':zona.html.twig',array(
                                                                                                                        'competencia' => $entity->getZona()->getLiga(),
                                                                                                                        'zona'        => $entity->getZona()
                                                                                                                      )
                                );
        }
    }

    /**
     * Creates a form to create a Partido entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Partido $entity)
    {
        return $this->createForm(new PartidoType(), $entity, array(
            'action' => $this->generateUrl('zona_partido_create', array('id' => $entity->getZona()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                    )
        ));
    }

    /**
     * Creates a form to delete a Plaza entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Partido $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('zona_partido_delete_flush', array('id' => $entity->getId())),
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
     * Creates a form to delete a Plaza entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createResetResultadoForm(Partido $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('zona_partido_resultado_reset_flush', array('id' => $entity->getId())),
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
     * Creates a form to edit a Partido entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Partido $entity)
    {
        return $this->createForm(new PartidoType(), $entity, array(
            'action' => $this->generateUrl('zona_partido_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                    )
        ));
    }
    
    /**
    * Creates a form to edit a Resultado Partido entity.
    *
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditResultadoForm(Partido $entity)
    {
        $form = $this->createForm($entity->getFormType(), $entity, array(
            'action' => $this->generateUrl('zona_partido_update_resultado', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$entity->getIdReload()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * @Route("/{zona}/new", name="zona_partido_new", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PARTIDO_NEW')")
     * @Template()
     */
    public function newAction(Request $request, Zona $zona)
    {
        if (!$zona)
        {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($zona->getLiga()->getEtapa()->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $entity = $zona->newPartido();
        $entity->setNombre("Partido ".(count($zona->getPartidos())+1)." - ".$zona);
        
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            if ($entity->getPlaza1()->getId() == $entity->getPlaza2()->getId())
            {
                return new JsonResponse(array('success' => false, 'msj' => 'Los equipos deben ser distintos.'));
            }
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.', 'debug'=> $e->getMessage()));
            } 
        }
        return array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                    );
    }

    /**
     * @Route("/{entity}/edit", name="zona_partido_edit", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PARTIDO_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Partido $entity)
    {
        if (!$entity)
        {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entity->setUpdatedBy($this->getUser());
            
            if ($entity->getPlaza1()->getId() == $entity->getPlaza2()->getId())
            {
                return new JsonResponse(array('success' => false, 'msj' => 'Los equipos deben ser distintos.'));
            }            
            try{
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            }catch (\Exception $e){
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }
        
        return array(
                        'entity' => $entity,
                        'form'   => $editForm->createView(),
                    );
    }

    /**
     * @Route("/{entity}/remove", name="zona_partido_delete", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PLAZA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Partido $entity)
    {
        if (!$entity)
        {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
    
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser eliminada.'));
            }
        }
        
        return array(
                        'entity' => $entity,
                        'form' => $form->createView()  
                    );
    }
    
    /**
     * @Route("/{entity}/edit/resultado", name="zona_partido_edit_resultado", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PARTIDO_EDIT_RESULTADO')")
     */
    public function editResultadoAction(Request $request, Partido $entity)
    {
        if (!$entity)
        {
            $this->addFlash('primary', 'No existe el Partido.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }

        $editForm = $this->createEditResultadoForm($entity);
        $editForm->handleRequest($request);
                                 
        if($editForm->isSubmitted() && $editForm->isValid())
        {
            $entity->setUpdatedBy($this->getUser());

            if ($entity->getResultadoLocal() < 0 || $entity->getResultadoVisitante() < 0)
            {
                return new JsonResponse(array('success' => false, 'msj' => 'El resultado del partido no es válido.'));
            }
            $entity->update();
            
            try{
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }


        }
        return $this->render('ResultadoBundle:Partido:'.$entity->getTemplate(), array(
                'entity' => $entity,
                'form'   => $editForm->createView(),
            )
        );
    }
   
    /**
     * @Route("/{entity}/partido/reset", name="zona_partido_resultado_reset", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_PARTIDO_EDIT_RESULTADO')")
     * @Template("ResultadoBundle:Partido:delete.html.twig")
     */
    public function resetResultadoAction(Request $request, Partido $entity)
    {
        if (!$entity)
        {
            $this->addFlash('primary', 'No existe el Partido.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getEvento()))
        {
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entity->resetResultado();
            try{
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'El resultado del partido fue reseteado.'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser reseteada.'));
            }
        }
        
        return array(
                        'entity' => $entity,
                        'form' => $form->createView()  
                    );
    }    
}
