<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Zona;
use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Partido;
use ResultadoBundle\Entity\Cronograma;
use ResultadoBundle\Entity\CronogramaPartido;
use ResultadoBundle\Form\CronogramaType;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaLiga;

use CommonBundle\PDFs\DocumentoPDF;
/**
 * Cronograma controller.
 *
 * @Route("/resultados/cronograma/partido")
 * @Security("has_role('ROLE_CRONOGRAMA') and has_role('ROLE_PARTIDO')")
 */
class CronogramaPartidoController extends Controller
{
    /**
     * Displays a form to edit an existing Cronograma entity.
     *
     * @Route("/{id}/edit", name="cronograma_partido_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_EDIT')")
     * @Template("ResultadoBundle:Cronograma:edit.html.twig")
     */
    public function editAction(Request $request, Cronograma $cronograma)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($cronograma->getEvento())){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return new JsonResponse(array('success' => false, 'reload' =>true));        
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!$cronograma) {
            $this->addFlash('primary', 'No existe el Cronograma.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }

        $editForm = $this->createEditForm($cronograma);

        return array(
            'entity' => $cronograma,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Partido entity.
    *
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cronograma $cronograma)
    {
        $form = $this->createForm(new CronogramaType(), $cronograma, array(
            'action' => $this->generateUrl('cronograma_partido_update', array('id' => $cronograma->getId())),
            'method' => 'POST',
            'attr' => array(
                            'data-idreload' => "reload-parcial-".$cronograma->getIdReload()
                    )
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
    /**
     * Edits an existing Partido entity.
     *
     * @Route("/{id}/edit", name="cronograma_partido_update")
     * @Method("POST")
     * @Security("has_role('ROLE_CRONOGRAMA_EDIT')")
     * @Template("ResultadoBundle:Cronograma:edit.html.twig")
     */
    public function updateAction(Request $request, Cronograma $cronograma)
    {
        if (!$cronograma) {
            $this->addFlash('primary', 'No existe el cronograma.');
            return new JsonResponse(array('success' => false, 'reload' =>true));
        }
        
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($cronograma);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $cronograma->setUpdatedBy($this->getUser());
            $cronograma->setUpdatedAt(new \DateTime());

            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'.$e->getMessage()));
            }
        }

        return array(
            'entity' => $cronograma,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Print a Cronograma entity.
     *
     * @Route("/print/{evento}", name="cronograma_partido_print", defaults={"evento" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_PRINT')")
     */    
    public function printAction(Request $request, Evento $evento)
    {
        $em = $this->getDoctrine()->getManager();
        $cronogramas = $evento->getCronogramas();
        $pdf = new DocumentoPDF();
        $pdf->setTextoHeader('Cronograma');
        $pdf->setMargenLeft(10);
        $pdf->setMargenRight(10);
        $pdf->init();
        $pdf->writeHTML('
                        <div style="text-align:center">
                            <b>'.$evento.'</b><br>
                        </div>');
        if (count($cronogramas)>0){
            $trs = '<table cellspacing="0" cellpadding="10"><tr>
                        <th style="border-bottom: 1px solid silver;width: 25%" align="center"><b>Fecha</b></th>
                        <th style="border-bottom: 1px solid silver;width: 75%"><b>Descripción</b></th>
                    </tr>';
            foreach($cronogramas as $item){
                $trs.='
                    <tr>
                        <td class="borderBottom" align="center">'.$item->getRaw().'</td>
                        <td class="borderBottom">'.$item->getDescripcion().'</td>
                    </tr>
                ';
            }
            $pdf->writeHTML('<style>
                                .borderBottom{
                                    border-bottom: 1px solid silver;
                                }
                                td{
                                    vertical-align:middle;
                                }
                            </style>
                            <table cellspacing="0" cellpadding="10">'.$trs.'</table>', true, false, true, false, '');
        }
        $trs = '<tr>
                    <th align="center" class="borderBottom" style="width: 10%">Obs.</th>
                    <th align="right" class="borderBottom" style="width: 31%">Equipo</th>
                    <th class="borderBottom" style="width: 4%"></th>
                    <th align="center" class="borderBottom" style=";width: 20%">vs</th>
                    <th class="borderBottom" style="width: 4%"></th>
                    <th align="left" class="borderBottom" style="width: 31%">Equipo</th>
                </tr>';
        $cont=0;
        foreach($evento->getPartidos() as $item){
            $style = "";
            $cont++;
            if ($cont > 11){
                $cont = 0;
                $style = "page-break-after:always";
            }
            $detalleCronograma = $item->getCronograma()->getRaw();
            if (method_exists($item,"getTanteador")){
                $detalleCronograma.='<span style="font-size:8px">'.$item->getTanteador().'</span>';
            }
            $trs.='
                <tr style="'.$style.'">
                    <td class="borderBottom" style="font-size:8px;vertical-align:middle" align="center">'.$item->getNivelTexto().'</td>
                    <td align="right" class="borderBottom" style="font-size:10px;vertical-align:middle">'.$item->getPlaza1()->getNombreCompetenciaMinRaw().'</td>
                    <td class="borderBottom">'.$item->getResultadoLocal().'</td>
                    <td align="center" class="borderBottom">'.$detalleCronograma.'</td>
                    <td class="borderBottom">'.$item->getResultadoVisitante().'</td>
                    <td align="left" class="borderBottom" style="font-size:10px">'.$item->getPlaza2()->getNombreCompetenciaMinRaw().'</td>
                </tr>
            ';
        }
        //$pdf->writeHTML();
        $pdf->writeHTML('<style>
                            .borderBottom{
                                border-bottom: 1px solid silver;
                            }
                            td{
                                vertical-align:middle;
                            }
                        </style>
                        <table cellspacing="1" cellpadding="5" border="">'.$trs.'</table>', true, false, true, false, '');
        
        return new Response($pdf->Output('Cronograma.pdf','D'));
    }

    /**
     * Print a Cronograma entity.
     *
     * @Route("/print/troquelado/{evento}", name="cronograma_partido_troquelado_print", defaults={"evento" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_PRINT')")
     */    
    public function printTroqueladoAction(Request $request, Evento $evento)
    {
        //$em = $this->getDoctrine()->getManager();
        //$cronogramas = $evento->getCronogramas();
        $pdf = new DocumentoPDF();
        $pdf->init();
        $pdf->deletePage(1);
        $pdf->AddPage('L', 'A4');
        
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->SetFont('Times','B',16);   
        $pdf->MultiCell(200, 0, '##  '.$evento.' ##', 1, 'C', 0, -5, -200, '', true);
        $pdf->StopTransform();
        
        $cont = 0;
        $posY=[0,7,60,113,166];
        foreach($evento->getPartidos() as $item){
            if ($cont == 4){
                $cont = 0;
                $pdf->AddPage('L', 'A4');
                $pdf->StartTransform();
                $pdf->Rotate(90);
                $pdf->SetFont('Times','B',16);   
                $pdf->MultiCell(200, 0, '##  '.$evento.' ##', 1, 'C', 0, -5, -200, '', true);
                $pdf->StopTransform();
            }
                        //<th class="border thh" style="width:26%">MUNICIPIO</th>
                        //<th class="border thh" style="width:26%">RESULTADO</th>
                        //<th class="border thh" style="width:20%">OBSERVACIONES</th>
            $cont++;
            $pdf->SetY($posY[$cont]);
            //(integer)$item->getNombre()
            $pdf->writeHTML('
                <style>
                    .border{
                        border: 1px solid silver;
                        text-align:center;
                        font-size:10px;
                        overflow: hidden;
                    }
                    .thh{
                        background-color:#ddd;
                        font-weight: bold;
                    }
                </style>
                <table cellspacing="0" border="0" cellpadding="2" style="width:100%;">
                    <tr>
                        <th class="border thh" style="width:8%">FECHA</th>
                        <th class="border thh" style="width:19%">MUNICIPIO</th>
                        <th rowspan="6" style="width:5%"></th>
                        <th class="border thh" style="width:11%">EVENTO</th>
                        <th class="border thh" colspan="3" style="width:57%">'.strtoupper($evento).'</th>
                    </tr>
                    <tr>
                        <td class="border" >'.$item->getCronograma()->getFecha()->format('d/m h:i').'</td>
                        <td class="border" rowspan="2">'.$item->getPlaza1()->getNombreCompetenciaMinRaw().'</td>
                        <td class="border">'.$evento->getId().'</td>
                        <td class="border" rowspan="2">'.$item->getPlaza1()->getNombreCompetenciaMinRaw().'</td>
                        <td class="border" rowspan="2"></td>
                        <td class="border" rowspan="5"></td>
                    </tr>
                    <tr>
                        <th class="border thh" style="width:8%">PARTIDO</th>
                        <td class="border thh">PARTIDO</td>
                    </tr>
                    <tr>
                        <td class="border">'.(integer)$item->getNombre().'</td>
                        <td class="border thh">MUNICIPIO</td>
                        <td class="border">'.(integer)$item->getNombre().'</td>
                        <td class="border thh" >MUNICIPIO</td>
                        <td class="border thh" >RESULTADO</td>
                    </tr>
                    <tr>
                        <td class="border thh">ETAPA</td>
                        <td class="border" rowspan="2">'.$item->getPlaza2()->getNombreCompetenciaMinRaw().'</td>
                        <td class="border thh">ETAPA</td>
                        <td class="border" rowspan="2">'.$item->getPlaza2()->getNombreCompetenciaMinRaw().'</td>
                        <td class="border" rowspan="2"></td>
                    </tr>
                    <tr>
                    <td class="border">'.$item->getNivelTexto().'</td>
                        <td class="border">'.$item->getNivelTexto().'</td>
                    </tr>
                </table>
                ', true, false, true, false, '');
        }       
        
        return new Response($pdf->Output($evento.'.pdf','D'));
    }    
}
