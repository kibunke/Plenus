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
 * EtapaRegional controller.
 *
 * @Route("/etapaRegiona")
 * @Security("has_role('ROLE_RESULTADO_ETAPA_REGIONAL')")
 */
class EtapaRegionalController extends Controller
{
    /**
     * List all Competidor entities.
     *
     * @Route("/list", name="resultados_etapaRegional_list")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_ETAPA_REGIONAL_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Add a Competidor entity into a EtapaRegional collection.
     * @Route("/ganador/equipo/{equipo}/toggle", name="resultados_etapaRegiona_competidor_ganador", condition="request.isXmlHttpRequest()", defaults={"equipo":"__00__"})
     * @Method("POST")
     */
    public function ganadorAction(Request $request, Equipo $equipo)
    {
        try{
            if ($this->canEdit($equipo)){
                $em = $this->getDoctrine()->getManager();
                $evento = $equipo->getEtapaMunicipal()->getEvento();
                $etapa = $evento->agregarEquipoClasificadoEtapaRegional($equipo);
                $em->persist($etapa);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' =>'OK'));
            }
        }catch (\Exception $e) {
            return new JsonResponse(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    private function canEdit($equipo)
    {
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_RESULTADO_ETAPA_REGIONAL_EDIT')){
            throw new \Exception('Plenus: No tiene los permisos necesarios para realizar esta acción.');
        }
        if (!$equipo->getEtapaMunicipal()){
            throw new \Exception('Plenus: El equipo no participó en la etapa regional.');
        }
        $evento = $equipo->getEtapaMunicipal()->getEvento();
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
