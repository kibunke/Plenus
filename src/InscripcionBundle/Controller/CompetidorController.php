<?php

namespace InscripcionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\PlanillaEstado;
use ResultadoBundle\Entity\Competidor;
/**
 * Competidor controller.
 *
 * @Route("/inscripcion/competidor")
 * @Security("has_role('ROLE_INSCRIPCION_COMPETIDORES_LIST')")
 */
class CompetidorController extends Controller
{
    /**
     * @Route("/competidor/list", name="competidor_list")
     * @Template()
     */
    public function listCompetidorAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/competidor/list/datatable", name="competidor_list_datatable")
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
     * @Route("/combinar", name="competidor_combinar", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     * @Security("has_role('ROLE_INSCRIPCION_COMPETIDORES_COMBINAR')")
     */
    public function combinarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $competidores = $em->getRepository('ResultadoBundle:Competidor')->find(["id"]);
        var_dump($request->request->get('ids'));die;
        return array();
    }

    private function validarCombinacion($competidores)
    {
        $arrMunicipios = [];
        foreach ($competidores as $competidor) {
            foreach ($competidor->getPlanillas() as $planilla) {
                $arrMunicipios[$planilla->getMunicipio()->getId()] = true;
            }
        }
        if (count($arrMunicipios) > 1){
            return "";
        }
    }
}
