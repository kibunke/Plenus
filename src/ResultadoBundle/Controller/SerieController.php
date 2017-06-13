<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Serie;
use ResultadoBundle\Form\SerieType;
use ResultadoBundle\Form\PlazaSerieMarcaType;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaSerie;
use ResultadoBundle\Entity\PlazaSerie;
/**
 * Serie controller.
 *
 * @Route("/resultados/etapa/serie")
 * @Security("has_role('ROLE_SERIE')")
 */
class SerieController extends Controller
{
    private $letras = ["1ra","2da","3ra","4ta","5ta","6ta","7ma","8va","9na","10ma"];
    
    private function isValidAction($entity)
    {        
        /**
         * CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO
         * $entity puede ser tanto un objeto Serie como uno CompetenciaSerie
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
     * @Route("/{entity}/plazas", name="serie_reload", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function showDetalleAction(Request $request, Serie $entity)
    {
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render('ResultadoBundle:'.$entity->getCompetencia()->getFolder().':serie.html.twig',array(
                                                                                                                 'competencia' => $entity->getCompetencia(),
                                                                                                                 'serie'       => $entity
                                                                                                               )
                            );
    }
    
    /**
     * Creates a form to create a Serie entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Serie $entity)
    {
        return $this->createForm( new SerieType(),
                                  $entity,
                                  array(
                                         'action' => $this->generateUrl('serie_create', array('id' => $entity->getCompetencia()->getId())),
                                         'method' => 'POST',
                                         'attr'   => array('data-idreload' => "reload-total-".$entity->getCompetencia()->getId())
                                        )
                                  );
    }

    /**
     * Displays a form to create a new Serie entity.
     *
     * @Route("/{competencia}/new", name="serie_new" , condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_SERIE_NEW')")
     * @Template()
     */
    public function newAction(Request $request, CompetenciaSerie $competencia)
    {
        if(!$this->isValidAction($competencia))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }
        
        $entity = new Serie($this->getUser());
        $entity->setNombre($this->letras[count($competencia->getSeries())]." Serie");
        $entity->setCompetencia($competencia);

        $form = $this->createCreateForm($entity);
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
     * Displays a form to edit an existing Serie entity.
     *
     * @Route("/{entity}/edit", name="serie_edit", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_SERIE_EDIT')")
     * @Template()
     */
    public function editAction(Request $request,Serie $entity)
    {
        if(!$this->isValidAction($entity))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entity->setUpdatedBy($this->getUser());
            try{
                $this->getDoctrine()->getManager()->flush();
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
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Serie $entity)
    {
        return $this->createForm( new SerieType(),
                                  $entity,
                                  array(
                                          'action' => $this->generateUrl('serie_update', array('id' => $entity->getId())),
                                          'method' => 'POST',
                                          'attr' => array('data-idreload' => "reload-total-".$entity->getCompetencia()->getId())
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
    private function createDeleteForm(Serie $entity)
    {
        return $this->createFormBuilder( NULL,
                                         array(
                                                'action' => $this->generateUrl('serie_delete_flush', array('id' => $entity->getId())),
                                                'method' => 'DELETE',
                                                'attr'   => array('data-idreload' => "reload-total-".$entity->getCompetencia()->getId())
                                              )
                                        )
                    ->getForm()
                    ;
    }
    
    /**
     * Deletes a Serie entity.
     *
     * @Route("/{entity}/remove", name="serie_delete", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_SERIE_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Serie $entity)
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
     * Displays a form to edit an existing Serie Marca .
     *
     * @Route("/{entity}/edit/marca", name="serie_edit_marca", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Security("has_role('ROLE_SERIE_EDIT_MARCA')")
     * @Template("ResultadoBundle:Serie:edit.marca.html.twig")
     */
    public function editMarcaAction(Request $request, PlazaSerie $entity)
    {
        if(!$this->isValidAction($entity))
        {
            new JsonResponse(array('success' => false, 'reload' =>true)); 
        }

        $editForm = $this->createEditMarcaForm($entity);
        $editForm->handleRequest($request);
        
        if($editForm->isSubmitted() && $editForm->isValid())
        {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            if ($entity->getCompetencia()->validarMarca($entity->getMarca()))
            {
                $entity->getSerie()->recalcularOrdenNatural();
                try{
                    $this->getDoctrine()->getManager()->flush();
                    return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
                } catch (\Exception $e) {
                    return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
                }
            }else{
                return new JsonResponse(array('success' => false, 'msj' => 'El valor asignado a la marca del participante no es válido.'));
            }
        }
        
        return array(
                        'entity'  => $entity,
                        'form'    => $editForm->createView(),
                        'mensaje' => $entity->getSerie()->getCompetencia()->getAyudaMarca()
                    );
    }

    /**
     * Creates a form to edit a Zona entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditMarcaForm(PlazaSerie $entity)
    {
        return $this->createForm( new PlazaSerieMarcaType(),
                                  $entity,
                                  array(
                                         'action' => $this->generateUrl('serie_update_marca', array('id' => $entity->getId())),
                                         'method' => 'POST',
                                         'attr'   => array(
                                                           'data-idreload' => "reload-total-".$entity->getCompetencia()->getId(),
                                                           'data-mascara' => $entity->getCompetencia()->getMascara(),
                                                           'data-placaholder' => $entity->getCompetencia()->getDefaultMarca()
                                                          )
                                        )
                                );
    }
}
