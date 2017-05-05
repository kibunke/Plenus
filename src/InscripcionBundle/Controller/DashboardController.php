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
 * Dashboard controller.
 *
 * @Route("/inscripcion")
 * @Security("has_role('ROLE_INSCRIPCION_DASHBOARD')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="inscripcion_dashboard")
     * @Method({"GET"})
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/dashboard/inscriptos", name="inscripcion_dashboard_inscriptos")
     * @Method({"GET"})
     */
    public function inscritosWidgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $torneos = $em->getRepository('ResultadoBundle:Torneo')->findAll();
        $result = [];
        foreach ($torneos as $torneo){
            $result[$torneo->getId()] = $torneo->getJson();
        }
        $datos = $em->getRepository('InscripcionBundle:Planilla')->getDashboard();
        foreach ($datos as $dato){
            $result[$dato['id']]['datos']['inscriptos'][$dato['sexoNombre']] += $dato['sexo'];
            $result[$dato['id']]['datos']['inscriptos']['total'] += $dato['sexo'];
            $result[$dato['id']]['datos']['planillas'] += $dato['planillas'];
            $result[$dato['id']]['datos']['equipos'] += $dato['equipos'];
        }
        return $this->render('InscripcionBundle:Dashboard:inscriptos.widget.html.twig', array(
            'datos' => $result
        ));
    }

    /**
     * @Route("/dashboard/estados", name="inscripcion_dashboard_estados")
     * @Method({"GET"})
     */
    public function estadosWidgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $estados = $em->getRepository('InscripcionBundle:PlanillaEstado')->getInformeEstados();
        $estadosPosibles = PlanillaEstado::getEstadosPosibles();
        $total = 0;
        foreach ($estados as $estado){
            $total += $estado['cantidad'];
        }
        foreach ($estados as $estado){
            $estadosPosibles[$estado['nombre']]["cantidad"] = $estado['cantidad'];
            $estadosPosibles[$estado['nombre']]["porcentaje"] = round(($estado['cantidad']*100)/$total,1);
        }
        return $this->render('InscripcionBundle:Dashboard:estados.widget.html.twig', array(
            'estados' => $estadosPosibles
        ));
    }
}
