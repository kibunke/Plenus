<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use ResultadoBundle\Entity\Competidor;

/**
 * Default controller.
 *
 * @Route("/resultados")
 * @Security("has_role('ROLE_RESULTADO')")
 */
class DefaultController extends Controller
{
    /**
     * Lists all Evento entities.
     *
     * @Route("/eventos", name="resultado_evento_list")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/eventos/list/datatable", name="resultado_evento_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_RESULTADO_LIST')")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Evento')->datatable($request->request,$this->getUser());

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $evento){
            //var_dump($evento);
            $data['data'][] = array(
                "evento"  => array(
                                    "id" => $evento->getId(),
                                    "nombre" => $evento->getNombreCompletoRaw(),
                                    "orden" => $evento->getOrden(),
                                    "descripcion" => $evento->getDescripcion(),
                                    "completado" => $evento->getPorcentajeCompletado(),
                                    "equipos" => count($evento->getEtapaMunicipal()->getEquipos()),
                                    "definido" => $evento->getEtapaMunicipal()->getId() ? true : false
                            ),
                "actions"   => $this->renderView('ResultadoBundle:Evento:actions.html.twig', array('evento' => $evento)),
            );
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/informeDeFinalistas", name="resultados_informeFinalistas")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_PARTICIPANTE_LIST')")
     * @Template()
     */
    public function informeFinalistasAction()
    {
        $em = $this->getDoctrine()->getManager();
        //Participantes que estan en más de un equipo
        $competidores = $em->getRepository('ResultadoBundle:Competidor')->getCompetidoresSospechosos();
        $cont = $em->getRepository('ResultadoBundle:Competidor')->count();
        return array(
            'participantes' => $competidores,
            'contParticipantes' => $cont
        );
    }

    /**
     * @Route("/informeParticipante/{id}", name="resultados_informeParticipante")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_PARTICIPANTE_SHOW')")
     * @Template()
     */
    public function informeParticipanteAction(Participante $participante)
    {
        $em = $this->getDoctrine()->getManager();
        return array(
            'participante' => $participante
        );
    }

    /**
     * @Route("/dashboard", name="dashboard_resultado")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template()
     */
    public function dashboardAction()
    {
        return array();
    }

    /**
     * @Route("/dashboard/infEventos", name="dashboard_resultado_infEventos")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template("ResultadoBundle:Default:dashboard.infEventos.html.twig")
     */
    public function dashboardInfEventosAction()
    {
        $em = $this->getDoctrine()->getManager();

        $partidos = $em->getRepository('ResultadoBundle:Partido')->findAll();
        $plazasSinMedallero = $em->getRepository('ResultadoBundle:Plaza')->getAllSinMedallero();
        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        $eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        $eventos_definidos = [];
        $eventos_no_definidos = [];
        $disciplinas_eventos_no_definidos = [];
        $eventos_sin_equipos = [];
        $disciplinas_eventos_sin_equipos = [];
        foreach($eventos as $evento){
            if (count($evento->getEquipos())==0){
                $eventos_sin_equipos[]=$evento;
                if (!in_array($evento->getDisciplina(),$disciplinas_eventos_sin_equipos))
                    $disciplinas_eventos_sin_equipos[]=$evento->getDisciplina();
            }
            if (count($evento->getEtapas())!=0){
                $eventos_definidos[]=$evento;
            }
            else{
                $eventos_no_definidos[]=$evento;
                if (!in_array($evento->getDisciplina(),$disciplinas_eventos_no_definidos))
                    $disciplinas_eventos_no_definidos[]=$evento->getDisciplina();
            }
        }

        return array(
            'eventos' => $eventos,
            'eventos_sin_equipos' => $eventos_sin_equipos,
            'eventos_definidos' => $eventos_definidos,
            'eventos_no_definidos' => $eventos_no_definidos,
            'disciplinas_eventos_sin_equipos' => $disciplinas_eventos_sin_equipos,
            'disciplinas_eventos_no_definidos' => $disciplinas_eventos_no_definidos,
        );
    }

    /**
     * @Route("/dashboard/infPartidos", name="dashboard_resultado_infPartidos")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template("ResultadoBundle:Default:dashboard.infPartidos.html.twig")
     */
    public function dashboardInfPartidosAction()
    {
        $em = $this->getDoctrine()->getManager();

        $partidos = $em->getRepository('ResultadoBundle:Partido')->findAll();
        //$plazasSinMedallero = $em->getRepository('ResultadoBundle:Plaza')->getAllSinMedallero();
        //$plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        //$eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        $cronogramaSinDefinir = [];
        $partidosSinJugar = [];
        $partidosJugados = [];
        $eventos_cronograma_sin_definir = [];
        $partidosConPlazaLibre = [];
        foreach($partidos as $partido){
            if (!$partido->getPlaza1()->getEquipo() || !$partido->getPlaza2()->getEquipo()){
                $partidosConPlazaLibre[] = $partido;
            }
            if ($partido->jugado()){
                $partidosJugados[]=$partido;
            }else{
                $partidosSinJugar[]=$partido;
            }
            if (!$partido->getCronograma()->getFecha()){
                $cronogramaSinDefinir[]=$partido;
                if (!in_array($partido->getEvento(),$eventos_cronograma_sin_definir))
                    $eventos_cronograma_sin_definir[]=$partido->getEvento();
            }
        }

        return array(
            'cronogramaSinDefinir' => $cronogramaSinDefinir,
            'partidosSinJugar' => $partidosSinJugar,
            'partidosJugados' => $partidosJugados,
            'partidos' => $partidos,
            'eventos_cronograma_sin_definir' => $eventos_cronograma_sin_definir,
            'partidosConPlazaLibre' => $partidosConPlazaLibre,
        );
    }

    /**
     * @Route("/dashboard/infPlazas", name="dashboard_resultado_infPlazas")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template("ResultadoBundle:Default:dashboard.infPlazas.html.twig")
     */
    public function dashboardInfPlazasAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$partidos = $em->getRepository('ResultadoBundle:Partido')->findAll();
        $plazasSinMedallero = $em->getRepository('ResultadoBundle:Plaza')->getAllSinMedallero();
        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        //$eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        $plazasAsignadas = [];
        $plazasSinAsignar = [];
        $eventos_plazas_sin_definir = [];
        $disciplinas_plazas_sin_definir = [];
        foreach($plazasSinMedallero as $plaza){
            if ($plaza->getEquipo()){
                $plazasAsignadas[]=$plaza;
            }else{
                $plazasSinAsignar[]=$plaza;
                if (!in_array($plaza->getEvento(),$eventos_plazas_sin_definir))
                    $eventos_plazas_sin_definir[]=$plaza->getEvento();
                if (!in_array($plaza->getEvento()->getDisciplina(),$disciplinas_plazas_sin_definir))
                    $disciplinas_plazas_sin_definir[]=$plaza->getEvento()->getDisciplina();
            }
        }

        return array(
            'plazasSinMedallero' => $plazasSinMedallero,
            'plazasAsignadas' => $plazasAsignadas,
            'plazasSinAsignar' => $plazasSinAsignar,
            'eventos_plazas_sin_definir' => $eventos_plazas_sin_definir,
            'disciplinas_plazas_sin_definir' => $disciplinas_plazas_sin_definir,
        );
    }

    /**
     * @Route("/dashboard/infMedallero", name="dashboard_resultado_infMedallero")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template("ResultadoBundle:Default:dashboard.infMedallero.html.twig")
     */
    public function dashboardInfMedalleroAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$partidos = $em->getRepository('ResultadoBundle:Partido')->findAll();
        $plazasSinMedallero = $em->getRepository('ResultadoBundle:Plaza')->getAllSinMedallero();
        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        //$eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        $plazasMedalleroAsignadas = [];
        $plazasMedalleroSinAsignar = [];
        $eventosplazasMedalleroSinAsignar = [];
        $plazasMedalleroOro = [];
        $plazasMedalleroOroAsignado = [];
        $plazasMedalleroPlata = [];
        $plazasMedalleroPlataAsignado = [];
        $plazasMedalleroBronce = [];
        $plazasMedalleroBronceAsignado = [];
        foreach($plazasMedallero as $plazaMedallero){
            switch ($plazaMedallero->getOrden()){
                case 1:
                        $plazasMedalleroOro[] = $plazaMedallero;
                        if ($plazaMedallero->getEquipo()){
                            $plazasMedalleroOroAsignado[] = $plazaMedallero;
                            $plazasMedalleroAsignadas[] = $plazaMedallero;
                        }else{
                            $plazasMedalleroSinAsignar[] = $plazaMedallero;
                            if (!in_array($plazaMedallero->getEvento(),$eventosplazasMedalleroSinAsignar))
                                $eventosplazasMedalleroSinAsignar[] = $plazaMedallero->getEvento();
                        }
                    break;
                case 2:
                        $plazasMedalleroPlata[] = $plazaMedallero;
                        if ($plazaMedallero->getEquipo()){
                            $plazasMedalleroPlataAsignado[] = $plazaMedallero;
                            $plazasMedalleroAsignadas[] = $plazaMedallero;
                        }else{
                            $plazasMedalleroSinAsignar[] = $plazaMedallero;
                            if (!in_array($plazaMedallero->getEvento(),$eventosplazasMedalleroSinAsignar))
                                $eventosplazasMedalleroSinAsignar[] = $plazaMedallero->getEvento();
                        }
                    break;
                case 3:
                        $plazasMedalleroBronce[] = $plazaMedallero;
                        if ($plazaMedallero->getEquipo()){
                            $plazasMedalleroBronceAsignado[] = $plazaMedallero;
                            $plazasMedalleroAsignadas[] = $plazaMedallero;
                        }else{
                            $plazasMedalleroSinAsignar[] = $plazaMedallero;
                            if (!in_array($plazaMedallero->getEvento(),$eventosplazasMedalleroSinAsignar))
                                $eventosplazasMedalleroSinAsignar[] = $plazaMedallero->getEvento();
                        }
                    break;
            }
        }

        return array(
            'plazasMedallero' => $plazasMedallero,
            'plazasMedalleroAsignadas' => $plazasMedalleroAsignadas,
            'plazasMedalleroSinAsignar' => $plazasMedalleroSinAsignar,
            'eventosPlazasMedalleroSinAsignar' => $eventosplazasMedalleroSinAsignar,
            'plazasMedalleroOro' => $plazasMedalleroOro,
            'plazasMedalleroOroAsignado' => $plazasMedalleroOroAsignado,
            'plazasMedalleroPlata' => $plazasMedalleroPlata,
            'plazasMedalleroPlataAsignado' => $plazasMedalleroPlataAsignado,
            'plazasMedalleroBronce' => $plazasMedalleroBronce,
            'plazasMedalleroBronceAsignado' => $plazasMedalleroBronceAsignado,
        );
    }

    /**
     * @Route("/dashboard/medallero", name="dashboard_resultado_medallero")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_DASHBOARD')")
     * @Template("ResultadoBundle:Default:dashboard.medallero.html.twig")
     */
    public function dashboardMedalleroAction()
    {
        $em = $this->getDoctrine()->getManager();

        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        $listos = [];
        $municipios = [];
        $oro = []; $plata = []; $bronce = [];
        foreach($plazasMedallero as $plazaMedallero){
            if ($plazaMedallero->getEquipo()){
                $mun = $plazaMedallero->getEquipo()->getMunicipio();
                if (!in_array($mun->getId(), $listos)) {
                    $listos[]=$mun->getId();
                    $municipios[$mun->getId()]["region"] = "<small>".str_replace($mun->getRegionDeportiva(), "</small><strong>".$mun->getRegionDeportiva()."</strong><small>",  $mun->getCruceRegional())."</small>";
                    $municipios[$mun->getId()]["nombre"] = $mun->getNombre();
                    $municipios[$mun->getId()]["id"] = $mun->getId();
                    $municipios[$mun->getId()]["medallero"] = [0,0,0];
                    $oro[$mun->getId()] = 0;
                    $plata[$mun->getId()] = 0;
                    $bronce[$mun->getId()] = 0;
                }
                switch ($plazaMedallero->getOrden()){
                    case 1 : $oro[$mun->getId()] = $oro[$mun->getId()] + 1; break;
                    case 2 : $plata[$mun->getId()] = $plata[$mun->getId()] + 1; break;
                    case 3 : $bronce[$mun->getId()] = $bronce[$mun->getId()] + 1; break;
                }
                $municipios[$mun->getId()]["medallero"] = [$oro[$mun->getId()],$plata[$mun->getId()],$bronce[$mun->getId()]];
            }
        }

        $arr = array_multisort(
                    $oro, SORT_DESC, SORT_NUMERIC,
                    $plata, SORT_DESC, SORT_NUMERIC,
                    $bronce, SORT_DESC, SORT_NUMERIC,
                    $municipios
                );

        return array(
            'municipios' => $municipios,
        );
    }
}
