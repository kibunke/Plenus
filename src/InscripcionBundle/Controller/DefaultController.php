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
     * @Route("/competidor/list", name="competidor_list")
     * @Template()
     * @Security("has_role('ROLE_INSCRIPCION_COMPETIDORES_LIST')")
     */
    public function listCompetidorAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/competidor/list/datatable", name="competidor_list_datatable")
     * @Security("has_role('ROLE_INSCRIPCION_COMPETIDORES_LIST')")
     */
    public function listCompetidorDatatableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Competidor')->dataTable($request->request);

        $data=array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $competidor){
            // $planillas = array("total" => 0, "data"=>[]);
            // $municipio = "";
            // foreach ($competidor->getPlanillas() as $planilla){
            //     $planillas['total'] ++;
            //     $planillas['data'][] = $planilla->toArray();
            //     $municipio = $planilla->getMunicipio()->getNombre();
            // }
            // $segmentos = array("total" => 0, "data"=>[]);
            // foreach ($competidor->getSegmentos() as $segmento){
            //     $segmentos['total'] ++;
            //     $segmentos['data'][] = $segmento->toArray();
            // }

            $data['data'][] = array(
                                    "id"        => $competidor->getId(),
                                    "name"      => $competidor->getNombreCompleto(),
                                    "dni"       => $competidor->getDni(),
                                    "municipio" => $competidor->getMunicipio()->getNombre(),
                                    //"planillas" => $planillas,
                                    //"segmentos" => $segmentos,
                                    "auditoria" => array(
                                                        "createdBy" => $competidor->getCreatedBy() ? $competidor->getCreatedBy()->getNombreCompleto() : '-',
                                                        "municipio" => $competidor->getCreatedBy() ? $competidor->getCreatedBy()->getMunicipio()->getNombre() : '-',
                                                        "createdAt" => $competidor->getCreatedAt()->format('d/m/y H:i')
                                                    ),
                                    "actions"   => $this->renderView('InscripcionBundle:Default:actions.html.twig', array('entity' => $competidor))
                                );
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/competidor/{competidor}/show", name="competidor_show", condition="request.isXmlHttpRequest()", defaults={"competidor":"__00__"})
     * @Template("InscripcionBundle:Default:competidor.show.html.twig")

     */
    public function showCompetidorAction(Request $request, Competidor $competidor)
    {
        $planillas = array("total" => 0, "data"=>[]);
        $municipio = "";
        foreach ($competidor->getPlanillas() as $planilla){
            $planillas['total'] ++;
            $planillas['data'][] = $planilla->toArray();
            $municipio = $planilla->getMunicipio()->getNombre();
        }
        $segmentos = array("total" => 0, "data"=>[]);
        foreach ($competidor->getSegmentos() as $segmento){
            $segmentos['total'] ++;
            $segmentos['data'][] = $segmento->toArray();
        }
        return array('entity'=> $competidor);
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
     * @Template("InscripcionBundle:De")
     */
    public function consultaResumenRegionalAction(Request $request)
    {
        return array();
    }
}
