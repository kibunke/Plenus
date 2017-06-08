<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use TesoreriaBundle\Form\FondoType;
use TesoreriaBundle\Entity\Fondo;
use AcreditacionBundle\Entity\PersonalJuegos;
use AcreditacionBundle\Entity\Area;
use TesoreriaBundle\Form\MovimientoType;
use TesoreriaBundle\Entity\Movimiento;
use TesoreriaBundle\Entity\Egreso;
use TesoreriaBundle\Entity\Reservado;
use TesoreriaBundle\Entity\Completado;
use TesoreriaBundle\Entity\Recibo;

/**
 * Movimiento controller.
 *
 * @Route("/tesoreria/movimiento")
 * @Security("has_role('ROLE_TESORERIA_MOVIMIENTO')")
 */
class MovimientoController extends Controller
{
    /**
     * Lists all Movimientos entities.
     *
     * @Route("/list", name="tesoreria_movimiento_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $personal = $em->getRepository('AcreditacionBundle:PersonalJuegos')->findAll();
        $fondos = $em->getRepository('TesoreriaBundle:Fondo')->findAll();
        $areas = $em->getRepository('AcreditacionBundle:Area')->findAll();

        return array(
            'personal' => $personal,
            'fondos' => $fondos,
            'areas' => $areas
        );
    }

    /**
     * Consulta analiticoPersonal.
     *
     * @Route("/query/analiticoPersonal", name="tesoreria_movimiento_analiticoPersonal")
     * @Method("GET")
     * @Template()
     */
    public function analiticoPersonalAction() {
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('TesoreriaBundle:Movimiento')->findAll();
        $entities = $em->getRepository('AcreditacionBundle:PersonalJuegos')->findAll();
        return array(
            'personal' => $entities,
        );
    }

    /**
     * @Route("/list/datatable", name="tesoreria_movimiento_list_datatable")
     */
    public function listDataTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('TesoreriaBundle:DatosTesoreria')->datatable($request->request);
        $data=array(
            "draw"=> $request->request->get('draw'),
            "recordsTotal"=> $filter['total'],
            "recordsFiltered"=> $filter['count'],
            "data"=> array()
        );
        foreach ($filter['data'] as $persona){
            //$user = $log->getUsuario()?$log->getUsuario()->getNombreCompleto():'-';
            $data['data'][] = array(
                "activo"    => $persona->getActivo(),
                "persona"   => $persona->getDatosTesoreria()->toArray(false),
                "avatar"    => '',//$persona->getAvatar()->getArchivo(),
                "actions"   => ''
            );
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/json/personas/{area}", name="tesoreria_get_personas_area_json")
     */
    public function getPersonasPorAreaAction(Area $area)
    {
        $data = array();
        foreach ($area->getPersonal() as $persona){
            $data[] = $persona->getDatosTesoreria()->toArray(false);
        }
        return new JsonResponse(array('success' => true, 'data'=> $data));
    }

    /**
     * @Route("/list/areas", name="tesoreria_movimiento_list_areas")
     * @Template("TesoreriaBundle:Movimiento:areas.table.html.twig")
     */
    public function listAreasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $areas = $em->getRepository('AcreditacionBundle:Area')->findAll();

        return array(
            'areas' => $areas
        );
    }

    /**
     * Asigna Movimientos en estado RESERVA al personal que llega como aprametro.
     *
     * @Route("/asignar", name="tesoreria_movimiento_asignar")
     * @Method("POST")
     */
    public function asignarAction(Request $request)
    {
        $aSuccess = [];
        $aError = [];
        $aWarning = [];
        $jPersonas = json_decode($request->request->get('personas'));
        $jFondo = $request->request->get('fondo');

        $em = $this->getDoctrine()->getManager();
        $fondo = $em->getRepository('TesoreriaBundle:Fondo')->find($jFondo['id']);
        foreach($jPersonas as $jPersona){
            $persona = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($jPersona->id);
            if (!$persona->tieneMovimiento() && $persona->getRemuneracion()<$fondo->getMontoDisponible()){

                $movimiento = new Egreso;
                $movimiento->setEstado(new Reservado);
                $movimiento->setDestinatario($persona);
                $movimiento->setFondo($fondo);
                $movimiento->setMonto($persona->getRemuneracion());
                $movimiento->setCreatedBy($this->getUser());

                try{
                    $em->persist($movimiento);
                    $em->flush();
                    $aSuccess[] = $jPersona;
                } catch (Exception $e) {
                    $aError[] = $jPersona;
                }
            }else{
                $aWarning[] = $jPersona;
            }
        }
        return new JsonResponse(array('success' => true,'success' => $aSuccess, 'warning'=> $aWarning, 'error'=> $aError));
    }

    /**
     * Cambia el estado del Movimiento a completado.
     *
     * @Route("/pagar", name="tesoreria_movimiento_pagar")
     * @Method("POST")
     */
    public function pagarAction(Request $request)
    {
        $aSuccess = [];
        $aError = [];
        $aWarning = [];
        $jPersonas = json_decode($request->request->get('personas'));;

        $em = $this->getDoctrine()->getManager();
        foreach($jPersonas as $jPersona){
            $persona = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($jPersona->id);
            $movimiento = $persona->tieneMovimiento();
            if ($movimiento && !$movimiento->estaCompletado()){
                $recibo = new Recibo();
                $recibo->setCreatedBy($this->getUser());
                $movimiento->setRecibo($recibo);

                $movimiento->setEstado(new Completado);
                $movimiento->setUpdatedAt(new \DateTime);
                $movimiento->setUpdatedBy($this->getUser());

                try{
                    $em->flush();
                    $aSuccess[] = $jPersona;
                } catch (Exception $e) {
                    $aError[] = $jPersona;
                }
            }else{
                $aWarning[] = $jPersona;
            }
        }
        return new JsonResponse(array('success' => true,'success' => $aSuccess, 'warning'=> $aWarning, 'error'=> $aError));
    }

    /**
     * Cambia el estado del Movimiento de completado a reservado.
     *
     * @Route("/despagar", name="tesoreria_movimiento_despagar")
     * @Method("POST")
     */
    public function despagarAction(Request $request)
    {
        $aSuccess = [];
        $aError = [];
        $aWarning = [];
        $jPersonas = json_decode($request->request->get('personas'));;

        $em = $this->getDoctrine()->getManager();
        foreach($jPersonas as $jPersona){
            $persona = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($jPersona->id);
            $movimiento = $persona->tieneMovimiento();
            if ($movimiento && $movimiento->estaCompletado()){
                $recibo = $movimiento->getRecibo();
                $recibo->setAnulado(true);
                $recibo->setFechaAnulacion(new \DateTime());
                $recibo->setAnuladoPor($this->getUser());
                $movimiento->setRecibo(null);

                $movimiento->setEstado(new Reservado);
                $movimiento->setUpdatedAt(new \DateTime);
                $movimiento->setUpdatedBy($this->getUser());

                try{
                    $em->flush();
                    $aSuccess[] = $jPersona;
                } catch (Exception $e) {
                    $aError[] = $jPersona;
                }
            }else{
                $aWarning[] = $jPersona;
            }
        }
        return new JsonResponse(array('success' => true,'success' => $aSuccess, 'warning'=> $aWarning, 'error'=> $aError));
    }

    /**
     * elimina el Movimiento si esta en esto reservado.
     *
     * @Route("/desasignar", name="tesoreria_movimiento_desasignar")
     * @Method("POST")
     */
    public function desasignarAction(Request $request)
    {
        $aSuccess = [];
        $aError = [];
        $aWarning = [];
        $jPersonas = json_decode($request->request->get('personas'));;

        $em = $this->getDoctrine()->getManager();
        foreach($jPersonas as $jPersona){
            $persona = $em->getRepository('TesoreriaBundle:DatosTesoreria')->find($jPersona->id);
            $movimiento = $persona->tieneMovimiento();
            if ($movimiento && !$movimiento->estaCompletado()){

                $em->remove($movimiento);
                try{
                    $em->flush();
                    $aSuccess[] = $jPersona;
                } catch (Exception $e) {
                    $aError[] = $jPersona;
                }
            }else{
                $aWarning[] = $jPersona;
            }
        }
        return new JsonResponse(array('success' => true,'success' => $aSuccess, 'warning'=> $aWarning, 'error'=> $aError));
    }
}
