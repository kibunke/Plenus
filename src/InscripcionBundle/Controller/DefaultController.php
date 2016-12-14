<?php

namespace InscripcionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Default controller.
 *
 * @Route("/inscripcion")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="inscripcion")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $eventos = $em->getRepository('ResultadoBundle:Evento')->getAllByUserAndInscribe($this->get('security.context'));
        $instituciones = $em->getRepository('InscripcionBundle:Origen')->findAllOrderedByMunicipio();
        $arrInst=[];
        foreach($instituciones as $i){
            //asi si filtro tambien por tipo
            $arrInst[$i->getMunicipio()->getId()][$i->getClass()][]=$i->getNombre();
        }
        return array(
            'eventos' => $eventos,
            'instituciones' => json_encode($arrInst)
        );
    }

    /**
     * @Route("/mapa", name="inscripcion_mapa")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_LIST')")
     * @Template("InscripcionBundle:Default:mapa.html.twig")
     */
    public function mapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $result=$this->parserEventosData($em->getRepository('ResultadoBundle:Evento')->getAllConNombre($this->get('security.context')));
        
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
            }else{
                $result['ids']=NULL;
            }
            return new JsonResponse(
                                    array(
                                          "code" => 200,
                                          "success" => true,
                                          "response" => json_encode($em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeEvento($result['ids']))
                                          )
                                    );
        }
        return array(
                     'tree' => $result['tree'],
                     'list' => json_encode($em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeEvento($result['ids']))
                    );
    }
    
    /**
     * @Route("/consulta/analitico", name="consulta_analitico_inscripcion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA')")
     * @Template("InscripcionBundle:Inscripto:analitico.html.twig")
     */
    public function consultaAnaliticoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $result=$this->parserEventosData($em->getRepository('ResultadoBundle:Evento')->getAllConNombre($this->get('security.context')));
        
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
                'InscripcionBundle:Inscripto:analitico.table.html.twig',
                    array('analitico' => $em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeEvento($result['ids']))
            );
        }
        return array(
            'analitico' => $em->getRepository('ResultadoBundle:Evento')->getAnaliticoDeEvento($result['ids']),
            'tree' => $result['tree']
        );
    }
    
    /**
     * @Route("/consulta/resumenregional", name="consulta_resumenregional_inscripcion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA_REGIONAL')")
     * @Template("InscripcionBundle:Inscripto:resumenregional.html.twig")
     */
    public function consultaResumenRegionalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->parserEventosData($em->getRepository('ResultadoBundle:Evento')->getAllConNombre());
        $referencia=array('eventos'=>array());
        $resumen = array();
        $eventos = array();
        /*
        * controla que el array que viene por post no traiga ids de eventos q no puede ver
        * en tal caso reemplaza el array con el los ids que si puede ver
        */
        if ($request->getMethod() == 'POST') {
            $arr=$request->request->get('eventos');
            if (is_array($arr) && count($arr)>0){
                if (count(array_diff($arr, $result['ids']))==0){
                    $result['ids']=$arr;
                }
                $eventos=[];
                foreach ($result['ids'] as $id){
                    $e = $em->getRepository('ResultadoBundle:Evento')->find($id);
                    $eventos[$id]=$e->getNombreCompleto();
                }
                $resumen = $this->parserResumenRegionalData($em->getRepository('ResultadoBundle:Evento')->getResumenRegionalPorEventos($result['ids']),$result['ids']);
                $referencia=array_values($resumen)[0];
            }
            return $this->render(
                'InscripcionBundle:Inscripto:resumenregional.table.html.twig',
                array(
                      'resumen' => $resumen,
                      'eventos' => $eventos,
                      'referencia' => $referencia
                      )
            );            
        }
        return array(
            'resumen' => $resumen,
            'eventos' => $eventos,
            'referencia' => $referencia,
            'tree' => $result['tree'],
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
    
    private function parserResumenRegionalData($rows,$ids){
        $em = $this->getDoctrine()->getManager();
        $matriz = $this->parseMatriz($rows,$ids);
        foreach($rows as $row)
        {
            /* discarta los null que pueden venir en la query de eventos por el left joi*/
            if ($row['evento']>0){
                $matriz[$row['id']]['eventos'][$row['evento']]=$row['inscripcion'];
            }
        }
        return $matriz;
    }
    
    private function parseMatriz($rows,$ids){
        $matriz=array();
        $arrRow=array();
        foreach($ids as $id){
            $arrRow[$id]=0;
        }
        foreach($rows as $row){
            $cruce = "<small>".str_replace($row['regionDeportiva'], "</small><strong>".$row['regionDeportiva']."</strong><small>",  $row['cruceRegional'])."</small>";
            $matriz[$row['id']]=array('municipio'=>$row['nombre'],'region' => $row['regionDeportiva'],'regional' => $cruce,'eventos'=>$arrRow);
        }        
        return $matriz;
    }
}
