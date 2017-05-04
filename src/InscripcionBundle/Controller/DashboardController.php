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
 * Dashboard controller.
 *
 * @Route("/inscripcion")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="inscripcion_dashboard")
     * @Method({"GET"})
     * @Security("has_role('ROLE_INSCRIPCION_DASHBOARD')")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/dashboard/inscriptos", name="inscripcion_dashboard_inscriptos")
     * @Method({"GET"})
     * @Security("has_role('ROLE_INSCRIPCION_DASHBOARD')")
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
        //var_dump($result);die;
        return array(
            'datos' => $result
        );
    }
}
