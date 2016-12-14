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
    
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/plazas", name="serie_reload")
     * @Method("GET")
     */
    public function showDetalleAction(Request $request, Serie $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:'.$entity->getCompetencia()->getFolder().':serie.html.twig',
            array(
                  'competencia' => $entity->getCompetencia(),
                  'serie' => $entity
                )
        );
    }
    
    /**
     * Creates a new Serie entity.
     *
     * @Route("/{id}/create", name="serie_create")
     * @Method("POST")
     * @Security("has_role('ROLE_SERIE_NEW')")
     * @Template("ResultadoBundle:Serie:new.html.twig")
     */
    public function createAction(Request $request, CompetenciaSerie $competencia)
    {
        $entity = new Serie($this->getUser());
        $entity->setCompetencia($competencia);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($competencia->getEtapa()->getEvento())){
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
     * Creates a form to create a Serie entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Serie $entity)
    {
        $form = $this->createForm(new SerieType(), $entity, array(
            'action' => $this->generateUrl('serie_create', array('id' => $entity->getCompetencia()->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-total-".$entity->getCompetencia()->getId()
                            )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Serie entity.
     *
     * @Route("/{id}/new", name="serie_new")
     * @Method("GET")
     * @Security("has_role('ROLE_SERIE_NEW')")
     * @Template()
     */
    public function newAction(Request $request, CompetenciaSerie $competencia)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Serie();
        $entity->setNombre($this->letras[count($competencia->getSeries())]." Serie");
        $entity->setCompetencia($competencia);

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Serie entity.
     *
     * @Route("/{id}/edit", name="serie_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_SERIE_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Serie $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getCompetencia()->getEtapa()->getEvento())){
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
    private function createEditForm(Serie $entity)
    {
        $form = $this->createForm(new SerieType(), $entity, array(
            'action' => $this->generateUrl('serie_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-total-".$entity->getCompetencia()->getId()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Serie entity.
     *
     * @Route("/{id}", name="serie_update")
     * @Method("POST")
     * @Security("has_role('ROLE_SERIE_EDIT')")
     * @Template()
     */
    public function updateAction(Request $request, Serie $entity)
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
     * Deletes a Serie entity.
     *
     * @Route("/{id}/remove", name="serie_delete_flush")
     * @Security("has_role('ROLE_SERIE_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Serie $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                $this->addFlash('primary', 'No existe la Zona.');
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
    private function createDeleteForm(Serie $entity)
    {
        return $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('serie_delete_flush', array('id' => $entity->getId())),
                'method' => 'DELETE',
                'attr' => array(
                                'data-idreload' => "reload-total-".$entity->getCompetencia()->getId()
                        )
                )
            )
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Serie entity.
     *
     * @Route("/{id}/remove", name="serie_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_SERIE_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Serie $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getCompetencia()->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            $this->addFlash('primary', 'No existe la Serie.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        $form = $this->createDeleteForm($entity);
        
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
    
    /**
     * Displays a form to edit an existing Serie Marca .
     *
     * @Route("/{id}/edit/marca", name="serie_edit_marca")
     * @Method("GET")
     * @Security("has_role('ROLE_SERIE_EDIT_MARCA')")
     * @Template("ResultadoBundle:Serie:edit.marca.html.twig")
     */
    public function editMarcaAction(Request $request, PlazaSerie $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($entity->getCompetencia()->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Plaza.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditMarcaForm($entity);

        return array(
            'entity'=> $entity,
            'form'  => $editForm->createView(),
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
        $form = $this->createForm(new PlazaSerieMarcaType(), $entity, array(
            'action' => $this->generateUrl('serie_update_marca', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-total-".$entity->getCompetencia()->getId(),
                            'data-mascara' => $entity->getCompetencia()->getMascara(),
                            'data-placaholder' => $entity->getCompetencia()->getDefaultMarca()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
    /**
     * Edits an existing Serie entity.
     *
     * @Route("/{id}/marca", name="serie_update_marca")
     * @Method("POST")
     * @Security("has_role('ROLE_SERIE_EDIT_MARCA')")
     * @Template("ResultadoBundle:Serie:edit.marca.html.twig")
     */
    public function updateMarcaAction(Request $request, PlazaSerie $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            $this->addFlash('primary', 'No existe la Zona.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditMarcaForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            if ($entity->getCompetencia()->validarMarca($entity->getMarca())){
                $entity->getSerie()->recalcularOrdenNatural();
                try{
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
                } catch (\Exception $e) {
                    return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'));
                }
            }else{
                return new JsonResponse(array('success' => false, 'msj' => 'El valor asignado a la marca del participante no es válido.'));
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
}
