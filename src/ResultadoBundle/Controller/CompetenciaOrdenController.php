<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Etapa;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaOrden;

use CommonBundle\PDFs\DocumentoPDF;

/**
 * CompetenciaOrden controller.
 *
 * @Route("/resultados/competencia/orden")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaOrdenController extends CompetenciaController
{
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/reload", name="competenciaOrden_reload")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_SHOW')")
     */
    public function competenciaOrdenReloadAction(Request $request, Competencia $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:Competencia:Orden/edit.html.twig',
            array('competencia' => $entity)
        );
    }
    
    /**
     * Creates a new CompetenciaOrden entities by etapa.
     *
     * @Route("/{id}/orden/new", name="resultado_competenciaOrden_create")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createOrdenAction(Request $request, Etapa $etapa)
    {
        $form =  $this->createCompetenciaOrdenForm($etapa);
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
        
        if ($form->isValid()) {
            $competencia = new CompetenciaOrden($this->getUser());
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
            ->setAction($this->generateUrl('competenciaOrden_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * @Route("/{competencia}/remove", name="competenciaOrden_delete", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_COMPETENCIA_DELETE')")
     * @Template("ResultadoBundle:Etapa:delete.html.twig")
     */
    public function resetAction(Request $request,Competencia $competencia)
    {
        if (!$competencia)
        {
            throw $this->createNotFoundException('No existe la competencia.');
        }
        
        $form =  $this->createDeleteForm($competencia);                    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            foreach($competencia->getPlazas() as $item){
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
        
        return array('form' => $form->createView());
    }
    
    /**
     * Print a Finalistas.
     *
     * @Route("/print/{competencia}/{flag}", name="finalistas_orden_print", defaults={"flag" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_PRINT')")
     */    
    public function printEventoAction(Request $request, Competencia $competencia, $flag)
    {
        $evento = $competencia->getEtapa()->getEvento();
        $pdf    = new DocumentoPDF();
        $pdf->init();
        $pdf->writeHTML('<div style="text-align:center"><b>Orden de participación<br>'.$evento.'</b></div>');
        $pdf->SetFont('Helvetica', '', 10, '', 'false');
        $trs = $this->renderView('ResultadoBundle:Competencia:Orden/print.trs.evento.html.twig', array(
                                                                                                        'competencia' => $competencia,
                                                                                                        'flag'        => $flag
                                                                                                      )
                                );
        $pdf->writeHTML($trs, true, false, true, false, '');
        return new Response($pdf->Output('Finalistas '.$evento.'.pdf','D'));
    }
}
