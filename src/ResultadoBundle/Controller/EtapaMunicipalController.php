<?php

namespace ResultadoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Competidor;
use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Equipo;
// use CommonBundle\PDFs\DocumentoPDF;

/**
 * EtapaMunicipal controller.
 *
 * @Route("/etapaMunicipal")
 * @Security("has_role('ROLE_RESULTADO_ETAPA_MUNICIPAL')")
 */
class EtapaMunicipalController extends Controller
{
    /**
     * List all Competidor entities.
     *
     * @Route("/list", name="resultados_etapaMunicipal_list")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_ETAPA_MUNICIPAL_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/competidor/{competidor}/json", name="resultados_etapaMunicipal_competidor_json", condition="request.isXmlHttpRequest()", defaults={"competidor":"__00__"})
     */
    public function competidorJsonAction(Request $request, Competidor $competidor)
    {
        return new JsonResponse($competidor->toFullArray());
    }

    /**
     * Add a Competidor entity into a EtapaMunicipal collection.
     * @Route("/ganador/equipo/{equipo}/evento/{evento}/toggle", name="resultados_etapaMunicipal_competidor_ganador", condition="request.isXmlHttpRequest()", defaults={"equipo":"__EQ__","evento":"__EV__"})
     * @Method("POST")
     */
    public function ganadorAction(Request $request, Equipo $equipo, Evento $evento)
    {
        $equipo->setNombre($request->request->get('nombreEquipo'));
        try{
            if ($this->canEdit($equipo,$evento)){
                $em = $this->getDoctrine()->getManager();
                $etapa = $evento->agregarEquipoClasificadoEtapaMunicipal($equipo);
                $em->persist($etapa);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' =>'OK'));
            }
        }catch (\Exception $e) {
            return new JsonResponse(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    private function canEdit($equipo,$evento)
    {
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_RESULTADO_ETAPA_MUNICIPAL_EDIT')){
            throw new \Exception('Plenus: No tiene los permisos necesarios para realizar esta acción.');
        }

        if (!$equipo->getPlanilla()->isAprobada()){
            throw new \Exception('Plenus: El participante/equipo no tiene la planilla aprobada.');
        }
        if ($equipo->getPlanilla()->getSegmento() != $evento->getSegmento()){
            throw new \Exception('Plenus: El participante/equipo no pertenece al segmento del evento.');
        }
        if (!$user->hasRole('ROLE_ADMIN')){
            if ($user->hasRole('ROLE_COORDINADOR')){
                if (!$evento->hasAccess($user)){
                    throw new \Exception('Plenus: No tiene los permisos necesarios para operar sobre eventos que no coordina.');
                }
            }elseif ($user->hasRole('ROLE_ORGANIZADOR')){
                if ($user->getMunicipio() != $equipo->getPlanilla()->getMunicipio()){
                    throw new \Exception('Plenus: No tiene los permisos necesarios para operar sobre equipos que no pertenecen a su municipio.');
                }
            }else{
                throw new \Exception('Plenus: No tiene los permisos necesarios para realizar esta acción.');
            }
        }
        return true;
    }
}
