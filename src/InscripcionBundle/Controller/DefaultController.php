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
     * @Route("/consulta/resumenTorneo/{param}", name="consulta_resumenTorneo_inscripcion", defaults={"param" = null})
     * @Method({"GET"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA_TORNEO')")
     * @Template("InscripcionBundle:Default:resumenTorneo.html.twig")
     */
    public function consultaResumenTorneoAction(Request $request,$param)
    {
        $param = ($param == 'soloAprobadas') ? TRUE : FALSE;
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('ResultadoBundle:Torneo')->getResumenPorMunicipio($param);
        $municipios = $em->getRepository('CommonBundle:Municipio')->getAllArray();
        $resumen = [];
        $torneos = [];
        foreach ($data['torneos'] as $row) {
            $torneos[$row['id']] = array(
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'parcial' => 0
            );
        }
        foreach ($municipios as $municipio){
            $resumen[$municipio['id']] = array(
                "id" => $municipio['id'],
                "nombre" => $municipio['nombre'],
                'region' => $municipio['region'],
                "data" => $torneos
            );
        }
        foreach ($data['totalPorMunicipio'] as $row){
            $resumen[$row['municipioId']]['data'][$row['torneoId']]['parcial'] = $row['total'];
        }
        return array(
            'resumen' => $resumen,
            'data' => $data,
            'soloAprobadas' => $param
        );
    }

    /**
     * @Route("/consulta/resumenPorSegmento", name="consulta_resumenPorSegmento_inscripcion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_INSCRIPCION_CONSULTA_SEGMENTO')")
     * @Template("InscripcionBundle:Default:resumenPorSegmento.html.twig")
     */
    public function consultaResumenPorSegmentoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $referencia=array('segmentos'=>array());
        $resumen = array();
        $segmentos = array();
        /*
        * controla que el array que viene por post no traiga ids de eventos q no puede ver
        * en tal caso reemplaza el array con el los ids que si puede ver
        */
        if ($request->getMethod() == 'POST') {
            $arr = $request->request->get('eventos');
            if (is_array($arr) && count($arr)>0){
                $eventos=[];
                foreach ($arr as $id){
                    $seg = $em->getRepository('InscripcionBundle:Segmento')->find($id);
                    $segmentos[$id]=$seg->getNombreCompleto();
                }
                $resumen = $this->parserResumenPorSegmentoData($em->getRepository('InscripcionBundle:Segmento')->getResumenPorSegmentos($arr),$arr);
                $referencia = array_values($resumen)[0];
            }
            return $this->render(
                'InscripcionBundle:Default:resumenPorSegmento.table.html.twig',
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

    private function parserResumenPorSegmentoData($rows,$ids){
        $em = $this->getDoctrine()->getManager();
        $matriz = $this->parseMatriz($rows,$ids);
        foreach($rows as $row)
        {
            /* descarta los null que pueden venir en la query de eventos por el left join*/
            if ($row['segmento'] > 0){
                $matriz[$row['id']]['segmentos'][$row['segmento']] = $row['inscriptos'];
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
