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
     * Delete a Etapa entity.
     *
     * @Route("/{id}/remove", name="competenciaOrden_delete")
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
     * @Route("/{id}/delete", name="competenciaOrden_delete_flush")
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
                $em->remove($competencia);
                $em->flush();
                $this->addFlash('exito', 'La etapa fue reseteada con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'La etapa no pudo ser reseteada.');
            }
        }
        return new JsonResponse(array('success' => true, 'reload' =>true));
    }
    
    /**
     * Print a Finalistas.
     *
     * @Route("/print/{id}/{flag}", name="finalistas_orden_print", defaults={"flag" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_PRINT')")
     */    
    public function printEventoAction(Request $request, Competencia $competencia, $flag)
    {
        $evento = $competencia->getEtapa()->getEvento();
        $pdf = new DocumentoPDF();
        $pdf->init();
        $pdf->writeHTML('<div style="text-align:center"><b>Orden de participación<br>'.$evento.'</b></div>');
        $pdf->SetFont('Helvetica', '', 10, '', 'false');
        $trs = "";
        if (count($competencia->getPlazas())>0){
            if ($flag==0){
                $trs .= '<table cellspacing="0" cellpadding="10">
                            <tr>
                                <th style="border-bottom: 1px solid silver;width:10%" align="center"><b>#</b></th>
                                <th style="border-bottom: 1px solid silver;width:20%" align="center"><b>Región</b></th>
                                <th style="border-bottom: 1px solid silver;width:70%"><b>Plaza</b></th>
                            </tr>                        
                    ';
                $cont = 0;
                foreach($competencia->getPlazas() as $item){
                    $cont++;
                    $reg = "";
                    if ($item->getEquipo())
                        $reg = $item->getEquipo()->getMunicipio()->getCruceRegionalRaw();
                    $trs.='                    
                        <tr>
                            <td style="border-bottom: 1px solid silver;" align="center">'.$cont.'</td>
                            <td style="border-bottom: 1px solid silver;" align="center">'.$reg.'</td>
                            <td style="border-bottom: 1px solid silver;">'.$item->getNombreCompetenciaRaw().'</td>
                        </tr>';
                }
                $trs.='</table>';
            }elseif ($flag == 1){
                $trs = $this->plazasConNombre($competencia);
            }else{
                $trs = $this->plazasConNombreYDni($competencia);
            }
        }
        $pdf->writeHTML($trs, true, false, true, false, '');
        return new Response($pdf->Output('Finalistas '.$evento.'.pdf','D'));
    }
    
    private function plazasConNombreYDni($competencia){
        $trs = '<table cellspacing="0" cellpadding="10">
                    <tr>
                        <th style="border-bottom: 1px solid silver;width:10%" align="center"><b>#</b></th>
                        <th style="border-bottom: 1px solid silver;width:12%" align="center"><b>Región</b></th>
                        <th style="border-bottom: 1px solid silver;width:30%"><b>Plaza</b></th>
                        <th style="border-bottom: 1px solid silver;width:35%"><b>Participante</b></th>
                        <th style="border-bottom: 1px solid silver;width:13%">DNI</th>
                    </tr>                        
            ';
        $cont = 0;
        foreach($competencia->getPlazas() as $item){
            $cont ++;
            $rowspan =  0;
            $reg = "";
            if ($item->getEquipo()){
                $participantes = $item->getEquipo()->getParticipantes()->toArray();
                $rowspan = count($participantes);
                $reg = $item->getEquipo()->getMunicipio()->getCruceRegionalRaw();            
            }
            //
            $part='<td style="border-bottom: 1px solid silver;"></td><td style="border-bottom: 1px solid silver;"></td>';
            if ($participantes){
                  $primero = array_shift($participantes);
                $part='
                    <td style="border-bottom: 1px solid silver;">'.$primero->getNombreCompleto().'</td>
                    <td style="border-bottom: 1px solid silver;">'.$primero->getDocumentoNro().'</td>
                    ';
            }
            $trs.='                    
                <tr>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;" align="center">'.$cont.'</td>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;" align="center">'.$reg.'</td>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;">'.$item->getNombreCompetenciaRaw().'</td>
                    '.$part.'
                </tr>';
            foreach($participantes as $item1){
                $trs.='<tr>
                        <td style="border-bottom: 1px solid silver;">'.$item1->getNombreCompleto().'</td>
                        <td style="border-bottom: 1px solid silver;">'.$item1->getDocumentoNro().'</td>
                    </tr>';
            }
        }
        return $trs.='</table>';
    }
    
    private function plazasConNombre($competencia){
        $trs = '<table cellspacing="0" cellpadding="10">
                    <tr>
                        <th style="border-bottom: 1px solid silver;width:10%" align="center"><b>#</b></th>
                        <th style="border-bottom: 1px solid silver;width:15%" align="center"><b>Región</b></th>
                        <th style="border-bottom: 1px solid silver;width:30%"><b>Plaza</b></th>
                        <th style="border-bottom: 1px solid silver;width:35%"><b>Participante</b></th>
                    </tr>                        
            ';
        $cont = 0;
        foreach($competencia->getPlazas() as $item){
            $cont ++;
            $rowspan =  0;
            $reg = "";
            if ($item->getEquipo()){
                $participantes = $item->getEquipo()->getParticipantes()->toArray();
                $rowspan = count($participantes);
                $reg = $item->getEquipo()->getMunicipio()->getCruceRegionalRaw();            
            }
            $part='<td style="border-bottom: 1px solid silver;"></td>';
            if ($participantes){
                  $primero = array_shift($participantes);
                $part='<td style="border-bottom: 1px solid silver;">'.$primero->getNombreCompleto().'</td>';
            }
            $trs.='                    
                <tr>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;" align="center">'.$cont.'</td>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;" align="center">'.$reg.'</td>
                    <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;">'.$item->getNombreCompetenciaRaw().'</td>
                    '.$part.'
                </tr>';
            foreach($participantes as $item1){
                $trs.='<tr><td style="border-bottom: 1px solid silver;">'.$item1->getNombreCompleto().'</td></tr>';
            }
        }
        return $trs.='</table>';
    }
}
