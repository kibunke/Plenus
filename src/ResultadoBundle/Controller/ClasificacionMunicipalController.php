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

// use InscripcionBundle\Entity\Planilla;
// use InscripcionBundle\Entity\Cargada;
// use InscripcionBundle\Entity\Institucion;
// use InscripcionBundle\Entity\Individual;
// use InscripcionBundle\Entity\Equipo;
// use ResultadoBundle\Entity\DirectorTecnico;
// use InscripcionBundle\Form\PlanillaType;
// use InscripcionBundle\Entity\Segmento;
use ResultadoBundle\Entity\Competidor;
use ResultadoBundle\Entity\Evento;
// use CommonBundle\PDFs\DocumentoPDF;

/**
 * ClasificacionMunicipal controller.
 *
 * @Route("/clasificacionMunicipal")
 * @Security("has_role('ROLE_RESULTADO_CLASIFICACION_MUNICIPAL')")
 */
class ClasificacionMunicipalController extends Controller
{
    /**
     * List all Competidor entities.
     *
     * @Route("/list", name="resultados_clasificacionMunicipal_competidor_list")
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_CLASIFICACION_MUNICIPAL_LIST')")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/competidor/{competidor}/json", name="resultados_clasificacionMunicipal_competidor_json", condition="request.isXmlHttpRequest()", defaults={"competidor":"__00__"})
     */
    public function competidorJsonAction(Request $request, Competidor $competidor)
    {
        return new JsonResponse($competidor->toArray());
    }

    /**
     * show a Competidor entity.
     *
     * @Route("/competidor/{id}/show", name="resultados_clasificacionMunicipal_competidor_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function showAction(Competidor $entity)
    {
        // return $this->render($planilla->getTemplateShow(), array(
        //     'planilla' => $planilla,
        //     'json' => json_encode($planilla->getJson())
        // ));
    }

    /**
     * Add a Competidor entity into a Etapa entity.
     *
     * @Route("/ganador/competidor/{competidor}/evento/{evento}", name="resultados_clasificacionMunicipal_competidor_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function ganadorAction(Competidor $competidor, Evento $evento)
    {
        // return $this->render($planilla->getTemplateShow(), array(
        //     'planilla' => $planilla,
        //     'json' => json_encode($planilla->getJson())
        // ));
    }

    private function canEdit($entity)
    {

    }
}
