<?php

namespace AcreditacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Listado controller.
 *
 * @Route("/acreditacion/listados")
 * @Security("has_role('ROLE_ACREDITACION_LIST') or has_role('ROLE_DIRECTOR')")
 */
class ListadoController extends Controller {

    /**
     * Procesa la colección de areas en un arreglo asociativo
     */
    private function procesarAreas($areas) {
        $em = $this->getDoctrine()->getManager();
        $result = array();
        $i = 1;
        foreach ($areas as $area) {
            $result[$i]['area'] = $area;
            $result[$i]['acreditados'] = count($em->getRepository('AcreditacionBundle:PersonalJuegos')->getAcreditados($area->getId()));
            $result[$i]['cupoAcreditados'] = $area->getCupoMaxPersonal();
            $result[$i]['cupoHospedados'] = $area->getCupoMaxHoteleria();
            $result[$i]['hospedados'] = count($em->getRepository('AcreditacionBundle:PersonalJuegos')->getHospedados($area->getId()));
            $result[$i]['cupoTransportados'] = $area->getCupoMaxTransporte();
            $result[$i]['transportados'] = count($em->getRepository('AcreditacionBundle:PersonalJuegos')->getTransportados($area->getId()));
            $result[$i]['cupoPresupuesto'] = $area->getCupoMaxPresupuesto();
            $result[$i]['presupuestados'] = $this->getPresupuestados($area->getId());
            $i++;
        }
        return $result;
    }

    /**
     * 
     */
    private function getPresupuestados($idArea) {
        $em = $this->getDoctrine()->getManager();
        $personal = $em->getRepository('AcreditacionBundle:PersonalJuegos')->getAcreditados($idArea);
        $sum = 0;
        foreach ($personal as $per) {
            if (is_object($per->getDatosTesoreria()->getCategoriaPago())){
                if ($per->getDatosTesoreria()->getCategoriaPago()->getNombre() == '6') {
                    $sum = $sum + $per->getDatosTesoreria()->getPagoEspecifico();
                } else {
                    $sum = $sum + $per->getDatosTesoreria()->getCategoriaPago()->getMonto();
                }
            }
        }
        return $sum;
    }

    /**
     * Verifica si no se ha alcanzado la fecha limite de Acreditación
     * 
     * @return boolean
     */
    private function dentroFechaAreditacion() {
        $fecha_actual = date('Y-m-d');
        $em = $this->getDoctrine()->getManager();
        $parametrosJuegos = $em->getRepository('AcreditacionBundle:JuegosParametros')->getParam();
        return ((strtotime($parametrosJuegos->getFechaLimiteAcreditacion()->format('Y-m-d')) >= strtotime($fecha_actual)) ? true : false);
    }

    /**
     * 
     */
    private function procesarTotales($areas) {
        $cupoAcreditados = $cupoHospedados = $cupoTransportados = $cupoPresupuestado = $totales['acreditados'] = $totales['hospedados'] = $totales['transportados'] = $totales['presupuestado'] = 0;
        foreach ($areas as $area) {
            $totales['acreditados'] += $area['acreditados'];
            $cupoAcreditados += $area['cupoAcreditados'];
            $totales['hospedados'] += $area['hospedados'];
            $cupoHospedados += $area['cupoHospedados'];
            $totales['transportados'] += $area['transportados'];
            $cupoTransportados += $area['cupoTransportados'];
            $totales['presupuestado'] += $area ['presupuestados'];
            $cupoPresupuestado += $area['cupoPresupuesto'];
        }
        $totales['acreditadosPorc'] = ($totales['acreditados'] * 100) / $cupoAcreditados;
        $totales['hospedadosPorc'] = ($totales['hospedados'] * 100) / $cupoHospedados;
        $totales['transportadosPorc'] = ($totales['transportados'] * 100) / $cupoTransportados;
        $totales['presupuestado'] = ($totales['presupuestado'] * 100) / $cupoPresupuestado;
        return $totales;
    }

    /**
     * Lista todas las areas en donde es responsable el usuario
     * 
     * @Route("/", name="acreditacion_listado_area")
     * @Method("GET")
     * @Template("AcreditacionBundle:Listado:listado_areas.html.twig")
     */
    public function listarAreasAction() {
        $em = $this->getDoctrine()->getManager();
        $totales = array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_DIRECTOR')) {
            $areas = $em->getRepository('AcreditacionBundle:Area')->findAll();
            $areasProcesadas = $this->procesarAreas($areas);
            $totales = $this->procesarTotales($areasProcesadas);
        } else {
            $areas = $em->getRepository('AcreditacionBundle:Area')->getAreasResponsable($this->getUser()->getId());
            $areasProcesadas = $this->procesarAreas($areas);
        }
        return array(
            'totales' => $totales,
            'areas' => $areasProcesadas,
            'enFechaAcreditacion' => $this->dentroFechaAreditacion(),
        );
    }

    /**
     * Lista todos los usuario de un area determinada
     * 
     * @Route("/area/{id}", name="acreditacion_listado_acreditados")
     * @Method("GET")
     * @Template("AcreditacionBundle:Listado:listado_acreditados.html.twig")
     */
    public function listarAcreditadosAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $acreditados = $em->getRepository("AcreditacionBundle:PersonalJuegos")->getAcreditadosAll($id);
        $area = $em->getRepository("AcreditacionBundle:Area")->find($id);
        $session = $request->getSession();
        $session->set('urlback', $this->generateUrl('acreditacion_listado_acreditados', array('id' => $id)));
        return array(
            'acreditados' => $acreditados,
            'area' => $area,
            'enFechaAcreditacion' => $this->dentroFechaAreditacion(),
        );
    }

    /**
     * Lista todos los usuario acreditados en el sistema
     * 
     * @Route("/todos", name="acreditacion_listado_total")
     * @Method("GET")
     * @Template("AcreditacionBundle:Listado:listado_acreditados.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listarTodosAcreditadosAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $acreditados = $em->getRepository("AcreditacionBundle:PersonalJuegos")->findAll();
        return array(
            'acreditados' => $acreditados,
            'enFechaAcreditacion' => $this->dentroFechaAreditacion(),
        );
    }

    /**
     * Listado del personal hospedado
     * 
     * @Route("/hospedados", name="acreditacion_listado_hospedados")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:Listado:listado_hospedados.html.twig")
     */
    public function listarHospedadosAction() {
        $em = $this->getDoctrine()->getManager();
        $acreditados = $em->getRepository("AcreditacionBundle:PersonalJuegos")->getHospedadosAll();
        return array(
            'acreditados' => $acreditados
        );
    }

    /**
     * Lista todas las areas en donde es responsable el usuario
     * 
     * @Route("/transportados", name="acreditacion_listado_transportados")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("AcreditacionBundle:Listado:listado_transportados.html.twig")
     */
    public function listarTransportadosAction() {
        $em = $this->getDoctrine()->getManager();
        $acreditados = $em->getRepository("AcreditacionBundle:PersonalJuegos")->getTransportadosAll();
        return array(
            'acreditados' => $acreditados
        );
    }

}
