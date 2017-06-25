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
     * @Route("/list", name="competidor_list")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if (!$this->getUser()->hasRole('ROLE_INSCRIPCION_COMPETIDORES_LIST_ALL')){
            return $this->render('InscripcionBundle:Competidor:listCompetidor.html.twig', array());
        }
        return array();
    }

    /**
     * @Route("/list/datatable", name="competidor_list_datatable")
     */
    public function listCompetidorDatatableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Competidor')->dataTable($request->request,$this->getUser());

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
     * @Route("/{competidor}/show/ajax", name="competidor_show_ajax", condition="request.isXmlHttpRequest()", defaults={"competidor":"__00__"})
     * @Template("InscripcionBundle:Competidor:show.ajax.html.twig")
     */
    public function showAjaxAction(Request $request, Competidor $competidor)
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
     * @Route("/{competidor}/show", name="competidor_show", defaults={"competidor":"__00__"})
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETIDOR_SHOW')")
     * @Template()
     */
    public function showAction(Competidor $competidor)
    {
        $em = $this->getDoctrine()->getManager();
        return array(
            'competidor' => $competidor
        );
    }

    /**
     * @Route("/combinar", name="competidor_combinar", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     * @Security("has_role('ROLE_INSCRIPCION_COMPETIDORES_COMBINAR')")
     */
    public function combinarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $competidores = $request->request->get('ids');
        $competidorBase = array_shift($competidores);
        $competidorBase = $em->getRepository('ResultadoBundle:Competidor')->find($competidorBase);
        $competidores = $em->getRepository('ResultadoBundle:Competidor')->findBy(['id' => $competidores]);
        try{
            $this->validarCombinacion($competidorBase,$competidores);
        }catch(\Exception $e ){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => $e->getMessage(), 'debug' => $e->getMessage()));
        }

        foreach ($competidores as $competidor) {
            foreach ($competidor->getCompetidorEquipos() as $competidorEquipo) {
                $competidorEquipo->setCompetidor($competidorBase);
            }
            $em->remove($competidor->prepareToDelete());
        }
        try{
            $em->flush();
        }catch(\Exception $e ){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'No se pudo persistir la información', 'debug' => $e->getMessage()));
        }
        //var_dump(count($competidores));die;
        return new JsonResponse(array('success' => true, 'error' => false, 'message' => 'La combinación fue exitosa!'));
    }

    private function validarCombinacion($competidorBase,$competidores)
    {
        foreach ($competidores as $competidor) {
            foreach ($competidor->getPlanillas() as $planilla) {
                $planilla->validarCombinacion($competidorBase);
            }
        }
    }
}
