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
     * @Route("/list", name="inscripcion_list_segmento")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION')")
     * @Template()
     */
    public function indexAction()
    {
        //$em = $this->getDoctrine()->getManager();
        //
        //$eventos = $em->getRepository('ResultadoBundle:Evento')->getAllByUserAndInscribe($this->get('security.context'));
        //$instituciones = $em->getRepository('InscripcionBundle:Origen')->findAllOrderedByMunicipio();
        //$arrInst=[];
        //foreach($instituciones as $i){
        //    //asi si filtro tambien por tipo
        //    $arrInst[$i->getMunicipio()->getId()][$i->getClass()][]=$i->getNombre();
        //}
        return array(
            //'eventos' => $eventos,
            //'instituciones' => json_encode($arrInst)
        );
    }
    
    /**
     * @Route("/list/datatable", name="inscripcion_list_segmento_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Segmento')->datatable($request->request,$this->getUser(),$this->get('security.authorization_checker'));

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $segmento){
            $inscriptos = $em->getRepository('InscripcionBundle:Segmento')->getTotalInscriptos($segmento,$this->getUser());
            $inscriptos = $segmento->getTotalInscriptosFromQuery($inscriptos);
            $planillas = $em->getRepository('InscripcionBundle:Segmento')->getTotalPlanillas($segmento,$this->getUser());
            $data['data'][] = array(
                "id"        => $segmento->getId(),
                "segmento"  => $segmento->getNombreCompletoRaw(),
                "planillas"   => $planillas ? $planillas['cant'] : 0,
                //"coordinadores" => count($segmento->getPlanillas()),
                "inscriptos"=> '<span class="text-danger" title="Planillas en cualquier estado / Planillas es estado Aprobadas">'.$inscriptos['total'].'</span> / <small class="text-success">'.$inscriptos['aprobadas'].'</small>',
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
     * @Template()
     */
    public function consultaResumenRegionalAction(Request $request)
    {
        
    }
}
