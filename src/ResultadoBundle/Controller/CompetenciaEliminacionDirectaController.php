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
use ResultadoBundle\Entity\Partido;
use ResultadoBundle\Entity\Competencia;

/**
 * CompetenciaEliminacionDirecta controller.
 *
 * @Route("/resultados/competencia/eliminacionDirecta")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaEliminacionDirectaController extends CompetenciaController
{
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/reload", name="competenciaEliminacionDirecta_reload")
     * @Method("GET")
     */
    public function competenciaEliminacionDirectaReloadAction(Request $request, Competencia $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render('ResultadoBundle:Competencia:EliminacionDirecta/edit.html.twig',array('competencia' => $entity));
    }    
    /**
     * Creates a new CompetenciaEliminacionDirecta entities.
     *
     * @Route("/{id}/eliminacionDirecta/puntos/new", name="resultado_competenciaEliminacionDirecta_create")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaEliminacionDirectaAction(Request $request, Etapa $etapa)
    {
        $form =  $this->createCompetenciaEliminacionDirectaForm($etapa);
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
            $tipo = "ResultadoBundle\Entity\CompetenciaEliminacionDirecta".$disciplinaParam->tipo;
            
            $competencia = new $tipo($this->getUser());
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
     * Show a CompetenciaEliminacionDirectaTemplates options.
     *
     * @Route("/{id}/eliminacionDirecta/template/view", name="resultado_competenciaEliminacionDirecta_template_view")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template("ResultadoBundle:Competencia:EliminacionDirecta/new.html.twig")
     */
    public function competenciaEliminacionDirectaTemplateViewAction(Request $request, Competencia $competencia)
    {
        $form8vos  = $this->createCompetenciaEliminacionTemplateForm($competencia,"8vos");
        $form4tos  = $this->createCompetenciaEliminacionTemplateForm($competencia,"4tos");
        $formSemi  = $this->createCompetenciaEliminacionTemplateForm($competencia,"semi");
        $formFinal = $this->createCompetenciaEliminacionTemplateForm($competencia,"final");
        
        return array(
            'form8vos'  => $form8vos->createView(),
            'form4tos'  => $form4tos->createView(),
            'formSemi'  => $formSemi->createView(),
            'formFinal' => $formFinal->createView(),
        );
    }    
    
    /**
     * Creates a new CompetenciaEliminacionDirecta entities.
     *
     * @Route("/{competencia}/eliminacionDirecta/template/{desde}/apply", name="resultado_competenciaEliminacionDirecta_template_apply")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function competenciaEliminacionTemplateApplyAction(Request $request, Competencia $competencia, $desde)
    {
        $form =  $this->createCompetenciaEliminacionTemplateForm($competencia);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($competencia->getEtapa()->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE LA COMPETENCIA NO TENGA PARTIDOS CREADOS */
        if(count($competencia->getPartidos())>0){
            $this->addFlash('primary', 'Esta competencia ya tiene creados partidos. Antes de crear nuevos, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($form->getData()['tercero']){
                $desde = $form->getData()['desde']."-3ro";
            }else{
                $desde = $form->getData()['desde']."-";
            }
            $competencia->aplicarTemplate($this->getUser(),$desde);
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        $this->addFlash('primary', 'Ocurrio un error al tratar de aplicar el template. Contacte al administrador');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    /**
     * Creates a form to create a CompetenciaEliminacionDirectaPuntos entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaEliminacionTemplateForm(Competencia $competencia,$desde="4tos")
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_competenciaEliminacionDirecta_template_apply', array('id' => $competencia->getId(),'desde' => $desde)))
                    ->setMethod('POST')
                    ->add('tercero', 'checkbox', array(
                                                        'label'    => 'Juega 3er puesto ?',
                                                        'data'     => true,
                                                        'required' => false,
                                                      )
                         )
                    ->add('desde', 'text', array(
                                                    'label'    => 'desde',
                                                    'data'     => $desde,
                                                    'required' => false,
                                                )
                         )                    
                    ->getForm();
                    ;
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
            ->setAction($this->generateUrl('competenciaEliminacionDirecta_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * @Route("/{competencia}/remove", name="competenciaEliminacionDirecta_delete", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Template("ResultadoBundle:Etapa:delete.html.twig")
     */
    public function resetAction(Request $request,Competencia $competencia)
    {
        if(!$competencia)
        {
            throw $this->createNotFoundException('No existe la competencia.');
        }
        
        $form =  $this->createDeleteForm($competencia);                    
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            foreach($competencia->getPlazas() as $item)
            {
                $em->remove($item);
            }
            
            try{
                $em->flush();
                $em->remove($competencia);
                $em->flush();
                $this->addFlash('exito', 'La etapa fue reseteada con exito ');
                
                return new JsonResponse(array('success' => true, 'reload' =>true));
            } catch (Exception $e) {
                $this->addFlash('error', 'La etapa no pudo ser reseteada.');
            }
        }
        
        return array(
            'form' => $form->createView(),
        );
    }
}
