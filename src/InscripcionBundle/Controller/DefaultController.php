<?php

namespace InscripcionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Competidor;
/**
 * Default controller.
 *
 * @Route("/inscripcion")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/list", name="inscripcion_list_segmento")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION')")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/list/datatable", name="inscripcion_list_segmento_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Segmento')->dataTableInscripcion($request->request,$this->getUser(),$this->get('security.authorization_checker'));

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $segmento){
            $data['data'][] = array(
                "id"        => $segmento->getId(),
                "segmento"  => $segmento->getNombreCompletoRaw(),
                "planillas"   => 0 ,//$planillas ? $planillas['cant'] : 0,
                "inscriptos"=> '',
                "parametros"=> array(
                    "max" => $segmento->getMaxIntegrantes(),
                    "min" => $segmento->getMinIntegrantes(),
                    "reemplazos" => $segmento->getMaxReemplazos(),
                    "maxFecha" => $segmento->getMaxFechaNacimiento()->format("d/m/Y"),
                    "minFecha" => $segmento->getMinFechaNacimiento()->format("d/m/Y"),
                ),
                "actions"   => $this->renderView('InscripcionBundle:Default:segmentoActions.html.twig', array('entity' => $segmento)),
            );
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/mapa", name="inscripcion_mapa")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_LIST')")
     * @Template()
     */
    public function mapAction(Request $request)
    {

    }

    /**
     * @Route("/consulta/analitico", name="consulta_analitico_inscripcion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA')")
     * @Template()
     */
    public function consultaAnaliticoAction(Request $request)
    {

    }

    /**
     * @Route("/consulta/resumenregional", name="consulta_resumenregional_inscripcion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA_REGIONAL')")
     * @Template("InscripcionBundle:Default:resumenregional.html.twig")
     */
    public function consultaResumenRegionalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$result = $em->getRepository('InscripcionBundle:Segmento')->getTree());
        $referencia=array('segmentos'=>array());
        $resumen = array();
        $segmentos = array();
        /*
        * controla que el array que viene por post no traiga ids de eventos q no puede ver
        * en tal caso reemplaza el array con el los ids que si puede ver
        */
        if ($request->getMethod() == 'POST') {
            $arr=$request->request->get('eventos');
            if (is_array($arr) && count($arr)>0){
                // if (count(array_diff($arr, $result['ids']))==0){
                //     $result['ids']=$arr;
                // }
                $eventos=[];
                foreach ($arr as $id){
                    $seg = $em->getRepository('InscripcionBundle:Segmento')->find($id);
                    $segmentos[$id]=$seg->getNombreCompleto();
                }
                $resumen = $this->parserResumenRegionalData($em->getRepository('InscripcionBundle:Segmento')->getResumenRegionalPorSegmentos($arr),$arr);
                $referencia = array_values($resumen)[0];
            }
            return $this->render(
                'InscripcionBundle:Default:resumenregional.table.html.twig',
                array(
                      'resumen' => $resumen,
                      'segmentos' => $segmentos,
                      'referencia' => $referencia
                    )
            );
        }
        return array(
            'resumen' => $resumen,
            'segmentos' => $segmentos,
            'referencia' => $referencia,
        );
    }

    private function parserResumenRegionalData($rows,$ids){
        $em = $this->getDoctrine()->getManager();
        $matriz = $this->parseMatriz($rows,$ids);
        foreach($rows as $row)
        {
            /* descarta los null que pueden venir en la query de eventos por el left joi*/
            if ($row['segmento'] > 0){
                $matriz[$row['id']]['segmentos'][$row['segmento']] = $row['inscripcion'];
            }
        }
        return $matriz;
    }

    private function parseMatriz($rows,$ids)
    {
        $matriz=array();
        $arrRow=array();
        foreach($ids as $id){
            $arrRow[$id] = 0;
        }
        foreach($rows as $row){
            //$cruce = "<small>".str_replace($row['regionDeportiva'], "</small><strong>".$row['regionDeportiva']."</strong><small>",  $row['cruceRegional'])."</small>";
            $matriz[$row['id']]=array('municipio'=>$row['nombre'],'region' => $row['regionDeportiva'],'regional' => $row['regionDeportiva'],'segmentos'=>$arrRow);
        }
        return $matriz;
    }
}
