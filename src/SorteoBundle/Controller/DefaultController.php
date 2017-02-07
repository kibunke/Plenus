<?php

namespace SorteoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CommonBundle\PDFs\DocumentoPDF;

use ResultadoBundle\Entity\Evento;
use CommonBundle\Entity\Municipio;

/**
 * Default controller.
 *
 * @Route("/finalistas")
 * @Security("has_role('ROLE_SORTEO')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/eventos", name="sorteo_carga")
     * @Method("GET")
     * @Security("has_role('ROLE_SORTEO_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));

        return array(
            'eventos' => $eventos
        );
    }
    
    /**
     * Finds and displays a Finalistas info.
     *
     * @Route("/{id}/show", name="ganadores_evento_show")
     * @Method("GET")
     * @Security("has_role('ROLE_SORTEO_SHOW')")
     * @Template()
     */
    public function showAction(Request $request, Evento $entity)
    {
        return array(
            'evento' => $entity
        );
    }
    
    /**
     * Finds and displays a Finalistas detalle.
     *
     * @Route("/consulta/analitico/{municipio}/show", name="consulta_analitico_finalistas_detalle")
     * @Method("GET")
     * @Security("has_role('ROLE_SORTEO_CONSULTA')")
     * @Template("SorteoBundle:Default:analitico.detalle.html.twig")
     */
    public function consultaAnaliticoDetalleAction(Partido $municipio)
    {
        $em = $this->getDoctrine()->getManager();
        $eventos = $em->getRepository('ResultadoBundle:Evento')->getConEquiposDelMunicipio($municipio,$this->get('security.context'));
        return array(
            'eventos' => $eventos
        );
    }
    
    /**
     * @Route("/consulta/analitico", name="consulta_analitico_finalistas")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_SORTEO_CONSULTA')")
     * @Template("SorteoBundle:Default:analitico.html.twig")
     */
    public function consultaAnaliticoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $result=$this->parserEventosData($em->getRepository('ResultadoBundle:Evento')->getAllConNombre($this->get('security.context'),false));
        
        /*
        * controla que el array que viene por post no traiga ids de eventos q no puede ver
        * en tal caso reemplaza el array con el los ids que si puede ver
        */
        if ($request->getMethod() == 'POST') {
            $arr=$request->request->get('eventos');
            if (is_array($arr)){
                if (count(array_diff($arr, $result['ids']))==0){
                    $result['ids']=$arr;
                }
            }
            return $this->render(
                'SorteoBundle:Default:analitico.table.html.twig',
                    array('analitico' => $em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeFinalistas($result['ids']))
            );
        }
        return array(
            'analitico' => $em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeFinalistas($result['ids']),
            'tree' => $result['tree']
        );
    }
    
    private function parserEventosData($eventos){
        $em = $this->getDoctrine()->getManager();
        $tree='{"text":"Todos mis eventos","state" : {"selected" : true},"children":[';
        $torneoIdAux=0;
        $disciplinaIdAux=0;
        $eventosIds=[];
        foreach($eventos as $evento)
        {
            $aux=explode('-',$evento['nombre']);
            $eventosIds[]=$evento['evento'];
            $objDisciplina = $em->getRepository('ResultadoBundle:Disciplina')->find($evento['disciplina']);
            $aux[1] = $objDisciplina->getNombreCompleto();
            if ($torneoIdAux==0){
                $tree.='{"text":"'.$aux[0].'","children":[{"text":"'.$aux[1].'","children":[{"text":"'.$evento['evento'].' -'.$aux[2].'-'.$aux[3].'-'.$aux[4].'","icon" : "fa fa-thumb-tack text-green fa-lg","id":"ev-'.$evento['evento'].'"}';
                $disciplinaIdAux=$evento['disciplina'];
                $torneoIdAux=$evento['torneo'];
            }else{            
                if ($evento['torneo']==$torneoIdAux){
                    if ($evento['disciplina']==$disciplinaIdAux){
                        $tree.=',{"text":"'.$evento['evento'].' -'.$aux[2].'-'.$aux[3].'-'.$aux[4].'","icon" : "fa fa-thumb-tack text-green fa-lg","id":"ev-'.$evento['evento'].'"}';
                    }
                    else{
                        $disciplinaIdAux=$evento['disciplina'];
                        $tree.=']},{"text":"'.$aux[1].'","children":[{"text":"'.$evento['evento'].' -'.$aux[2].'-'.$aux[3].'-'.$aux[4].'","icon" : "fa fa-thumb-tack text-green fa-lg","id":"ev-'.$evento['evento'].'"}'; 
                    }
                }else{
                    $torneoIdAux=$evento['torneo'];
                    $disciplinaIdAux=$evento['disciplina'];
                    $tree.=']}]},{"text":"'.$aux[0].'","children":[{"text":"'.$aux[1].'","children":[{"text":"'.$evento['evento'].' -'.$aux[2].'-'.$aux[3].'-'.$aux[4].'","icon" : "fa fa-thumb-tack text-green fa-lg","id":"ev-'.$evento['evento'].'"}';
                }
            }
        }
        $tree.="]}]}]}";
        return array('tree'=>$tree,'ids'=>$eventosIds);
    }
    
    /**
     * Print a Finalistas.
     *
     * @Route("/print/{id}", name="finalistas_evento_print", defaults={"evento" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_SORTEO_PRINT')")
     */    
    public function printEventoAction(Request $request, Evento $evento)
    {
        $em = $this->getDoctrine()->getManager();
        $pdf = new DocumentoPDF();
        $pdf->init();
        $pdf->writeHTML('<div style="text-align:center"><b>'.$evento.'</b></div>');
        $pdf->SetFont('Helvetica', '', 10, '', 'false');
        $trs = "";
        if (count($evento->getEquipos())>0){
            $trs .= '<table cellspacing="0" cellpadding="10">
                        <tr>
                            <th style="border-bottom: 1px solid silver;width:12%"><b>Región</b></th>
                            <th style="border-bottom: 1px solid silver;width:25%"><b>Equipo</b></th>
                            <th style="border-bottom: 1px solid silver;width:38%"><b>Participante</b></th>
                            <th style="border-bottom: 1px solid silver;width:15%">DNI</th>
                        </tr>                        
                ';
            foreach($evento->getEquipos() as $item){
                $rowspan =  (String) count($item->getParticipantes());
                $participantes = $item->getParticipantes()->toArray();
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
                        <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;" align="center">'.$item->getMunicipio()->getCruceRegionalRaw().'</td>
                        <td rowspan="'.$rowspan.'" style="border-bottom: 1px solid silver;">'.$item->getNombreCompletoRaw().'</td>
                        '.$part.'
                    </tr>';
                foreach($participantes as $item1){
                    $trs.='<tr>
                            <td style="border-bottom: 1px solid silver;">'.$item1->getNombreCompleto().'</td>
                            <td style="border-bottom: 1px solid silver;">'.$item1->getDocumentoNro().'</td>
                        </tr>';
                }
            }
            $trs.='</table>';            
        }
        //echo $trs;
        //die();
        $pdf->writeHTML($trs, true, false, true, false, '');
        return new Response($pdf->Output('Finalistas '.$evento.'.pdf','D'));
    }     
}
