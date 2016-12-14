<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Dashboard controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_DASHBOARD')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $usersStats = $this->parserUserStatsData($em->getRepository('SeguridadBundle:Usuario')->getStats());
        $usersOnLine = $em->getRepository('SeguridadBundle:Usuario')->findBy(array('logueado' => 1));
        $users = $em->getRepository('SeguridadBundle:Usuario')->findBy(array('activo' => 1));
        $usersInactived = $em->getRepository('SeguridadBundle:Usuario')->findBy(array('ultimaOperacion' => NULL,'activo' => 1));
        
        exec('free -m -t', $response);
        $response=str_replace("Total: ","",$response[4]);
        $response=str_replace("       "," ",str_replace("        "," ",trim($response)));
        $memUsed = explode(" ",$response );
        return array(
                     'users' => $users,
                     'usersStats' => $usersStats,
                     'usersOnline' => $usersOnLine,
                     'usersInactived' => $usersInactived,
                     'memUsed' => $memUsed,
                );
    }
    
    /**
     * @Route("/dashboard/inscriptos", name="dashboard_inscriptos")
     * @Method("GET")
     * @Template("GestionBundle:Dashboard:dashboard.inscriptos.html.twig")
     */
    public function dashboardInscriptosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $totalInscriptos = $this->parserTotalInscriptosData($em->getRepository('InscripcionBundle:Inscripto')->countInscriptosByTorneo());
        $municipiosInactivos = $em->getRepository('InscripcionBundle:Inscripto')->getMunicipiosSinInscriptos();
        $progesoInscripcion = $this->parserUserStatsData($em->getRepository('InscripcionBundle:Inscripto')->getProgreso());
        
        return array(
                     'totalInscriptos' => $totalInscriptos,
                     'municipiosInactivos' => $municipiosInactivos,
                     'progesoInscripcion' => $progesoInscripcion
                     );
    }
    
    private function parserUserStatsData($userStats)
    {
        $aux=['cantidades'=>'','dias'=>''];
        $i=0;
        foreach ($userStats as $item)
        {
            $aux['cantidades'].=$item['cant'].',';
            $aux['dias'].=$i.':"'.$item['fecha'].'",';
            $i++;
        }
        $aux['cantidades']=substr($aux['cantidades'], 0, -1);
        $aux['dias']=substr($aux['dias'], 0, -1);
        return $aux;
    }
    
    private function parserTotalInscriptosData($inscriptos)
    {
        $totalInscriptos=['Deportes (Juveniles)'=>0,'Cultura (Adultos Mayores)'=>0,'Cultura (Juveniles)'=>0,'Deportes (Adultos Mayores)'=>0,'fem'=>0,'mas'=>0];
        foreach ($inscriptos as $item)
        {
            $totalInscriptos[$item['nombre']]=$item['total'];
            $totalInscriptos['fem']+=$item['fem'];
            $totalInscriptos['mas']+=$item['mas'];
        }
        return $totalInscriptos;
    }
    
    /**
     * @Route("/dashboard/finalistas", name="dashboard_finalistas")
     * @Method("GET")
     * @Template("GestionBundle:Dashboard:dashboard.finalistas.html.twig")
     */
    public function dashboardFinalistasAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$torneos = $em->getRepository('ResultadoBundle:Torneo')->getEventosPorTorneo();
        $eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        return array(
                     'plazas' => $this->parserPlazas($eventos)
                    );
    }
    
    private function parserPlazas($eventos)
    {
        $totalPlazas=[ 'Disponibles' => [
                                            'Deportes'=> [
                                                              'Juveniles' => 0,
                                                              'AdultosMayores' => 0,
                                                              'Especiales' => 0,
                                                              'Total' => 0
                                                      ],
                                            'Cultura' =>[
                                                              'Juveniles' => 0,
                                                              'AdultosMayores' => 0,
                                                              'Especiales' => 0,
                                                              'Total' => 0
                                                      ],
                                            'Total' => 0
                                      ],
                        'Finalistas' => [
                                            'Deportes'=> [
                                                              'Juveniles' => 0,
                                                              'AdultosMayores' => 0,
                                                              'Especiales' => 0,
                                                              'Total' => 0
                                                      ],
                                            'Cultura' =>[
                                                              'Juveniles' => 0,
                                                              'AdultosMayores' => 0,
                                                              'Especiales' => 0,
                                                              'Total' => 0
                                                      ],
                                            'Total' => 0
                                      ]                      
                    ];
        foreach ($eventos as $item)
        {
            $sum = 0;
            $cantEquipos = count($item->getEquipos());
            if ($item->getTorneo()->getArea()=="Deportes"){
                if ($item->getTorneo()->getSubArea()=="Adultos Mayores"){           
                    $totalPlazas["Disponibles"]["Deportes"]["AdultosMayores"] += 24;
                    $totalPlazas["Finalistas"]["Deportes"]["AdultosMayores"] += $cantEquipos;
                    $sum = 24;
                }else{
                    if ($item->getEventoAdaptado()){
                        $totalPlazas["Disponibles"]["Deportes"]["Especiales"]+=12;
                        $totalPlazas["Finalistas"]["Deportes"]["Especiales"] += $cantEquipos;
                    }else{
                        $totalPlazas["Disponibles"]["Deportes"]["Juveniles"]+=12;
                        $totalPlazas["Finalistas"]["Deportes"]["Juveniles"] += $cantEquipos;
                    }
                    $sum = 12;
                }
                $totalPlazas["Disponibles"]["Deportes"]["Total"] += $sum;
                $totalPlazas["Finalistas"]["Deportes"]["Total"] += $cantEquipos;
            }else{
                if ($item->getTorneo()->getSubArea()=="Adultos Mayores"){
                    $totalPlazas["Disponibles"]["Cultura"]["AdultosMayores"]+=24;
                    $totalPlazas["Finalistas"]["Cultura"]["AdultosMayores"] += $cantEquipos;
                    $sum = 24;
                }else{
                    if ($item->getEventoAdaptado()){
                        $totalPlazas["Disponibles"]["Cultura"]["Especiales"]+=12;
                        $totalPlazas["Finalistas"]["Cultura"]["Especiales"] += $cantEquipos;
                    }else{
                        $totalPlazas["Disponibles"]["Cultura"]["Juveniles"]+=12;
                        $totalPlazas["Finalistas"]["Cultura"]["Juveniles"] += $cantEquipos;
                    }
                    $sum = 12;
                }
                $totalPlazas["Disponibles"]["Cultura"]["Total"] += $sum;
                $totalPlazas["Finalistas"]["Cultura"]["Total"] += $cantEquipos;
            }
            $totalPlazas["Disponibles"]["Total"] += $sum;
            $totalPlazas["Finalistas"]["Total"] += $cantEquipos;
        }
        return $totalPlazas;
    }    
}
