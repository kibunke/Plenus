<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Etapa;
use ResultadoBundle\Entity\Competencia;

/**
 * CompetenciaSerie controller.
 *
 * @Route("/resultados/competencia/serie")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaSerieController extends CompetenciaController
{
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/reload", name="competenciaSerie_reload")
     * @Method("GET")
     */
    public function competenciaSerieReloadAction(Request $request, Competencia $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:Competencia:Serie/edit.html.twig',
            array('competencia' => $entity)
        );
    }
    
    /**
     * Creates a new CompetenciaSerie entities by etapa.
     *
     * @Route("/{id}/new", name="resultado_competenciaSerie_create")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createSerieAction(Request $request, Etapa $etapa)
    {
        $form =  $this->createCompetenciaSerieForm($etapa);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($etapa->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        /* CHEQUEA QUE LA ETAPA NO TENGA COMPETENCIAS CREADAS */
        if($etapa->getCompetencia()){
            $this->addFlash('primary', 'Esta etapa ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($etapa->getEvento()->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $tipo = "ResultadoBundle\Entity\CompetenciaSerie".$disciplinaParam->tipo;
            if (class_exists($tipo)){
                $competencia = new $tipo($this->getUser());
            }else{
                $this->addFlash('error', 'La operación no pudo completarse. La Configuración de la Disciplina es inválida.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }            
            $competencia->setNombre("Competencia para ".$etapa->getNombre());
            $competencia->setEtapa($etapa);
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($competencia);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    
    
    
    /**
     * Creates a form to delete a Etapa entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Competencia $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('competenciaSerie_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Delete a Etapa entity.
     *
     * @Route("/{id}/remove", name="competenciaSerie_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Template("ResultadoBundle:Etapa:delete.html.twig")
     */
    public function resetAction(Request $request,Competencia $competencia)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        $form =  $this->createDeleteForm($competencia);                    
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * Deletes a Etapa entity.
     *
     * @Route("/{id}/delete", name="competenciaSerie_delete_flush")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Method("DELETE")
     */
    public function resetFlushAction(Request $request, Competencia $competencia)
    {
        $form = $this->createDeleteForm($competencia);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if (!$competencia) {
                throw $this->createNotFoundException('No existe la competencia.');
            }
            //$em->remove($competencia);
            foreach($competencia->getPlazas() as $item){
                $em->remove($item);
            }
            try{
                $em->flush();
                foreach($competencia->getSeries() as $item){
                    $em->remove($item);
                }
                $em->flush();
                $em->remove($competencia);
                $em->flush();
                $this->addFlash('exito', 'La etapa fue reseteada con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'La etapa no pudo ser reseteada.');
            }
        }
        return new JsonResponse(array('success' => true, 'reload' =>true));
    }     
}
