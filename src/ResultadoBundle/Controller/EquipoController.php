<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use ResultadoBundle\Entity\Equipo;
use ResultadoBundle\Entity\EquiposCompetidores;
use ResultadoBundle\Form\EquipoType;
use ResultadoBundle\Entity\Evento;

/**
 * Default controller.
 *
 * @Route("/equipo")
 * @Security("has_role('ROLE_RESULTADO_EQUIPO')")
 */
class EquipoController extends Controller
{
    /**
     * Finds and displays a Equipo entity.
     *
     * @Route("/{equipo}/show", name="equipo_show", condition="request.isXmlHttpRequest()", defaults={"equipo":"__00__"})
     * @Method("GET")
     * @Security("has_role('ROLE_RESULTADO_EQUIPO_SHOW')")
     */
    public function showAction(Request $request, Equipo $equipo)
    {
        return $this->render('ResultadoBundle:'.$equipo->getType().':show.html.twig',array(
            'equipo' => $equipo
        ));

    }

    /**
     * Finds and displays a Equipo entity.
     *
     * @Route("/{equipo}/edit", name="equipo_edit")
     * @Method({"GET"})
     * @Security("has_role('ROLE_RESULTADO_EQUIPO_EDIT')")
     * @Template()
     */
    public function editAction(Request $request, Equipo $equipo)
    {
        return array(
            'equipo' => $equipo
        );
    }

    /**
     * Finds and displays a Equipo entity.
     *
     * @Route("/{equipoCompetidorSale}/sustitucion", name="equipo_competidor_sustitucion", defaults={"equipoCompetidorSale":"__00__"})
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_RESULTADO_EQUIPO_EDIT')")
     * @Template()
     */
    public function sustitucionAction(Request $request, EquiposCompetidores $equipoCompetidorSale)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $competidorEntra = $em->getRepository('ResultadoBundle:Competidor')->find($request->request->get('equipoEntra'));
            if (!$competidorEntra){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor no existe'));
            }
            $equipoCompetidorEntra = $equipoCompetidorSale->getEquipo()->isSustituto($competidorEntra);
            if (!$equipoCompetidorEntra){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor no es un sustituto posible en este equipo'));
            }
            try{
                $equipoCompetidorSale->setEntra($equipoCompetidorEntra);
                $equipoCompetidorEntra->setSale($equipoCompetidorSale);
                $equipoCompetidorSale->setUpdatedBy($this->getUser());
                $em->flush();
                return new JsonResponse(array('success' => true, 'error' => false, 'message' => 'La sustitución fue compeltada!.'));
            }catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'No se pudo persistir la información', 'debug' => $e->getMessage()));
            }
        }

        return array(
            'equipoCompetidor' => $equipoCompetidorSale
        );

    }
}
