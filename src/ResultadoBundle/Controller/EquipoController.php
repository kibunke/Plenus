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
            try{
                $em = $this->getDoctrine()->getManager();
                $equipo = $equipoCompetidorSale->getEquipo();
                $competidorEntra = $em->getRepository('ResultadoBundle:Competidor')->find($request->request->get('equipoEntra'));
                if (!$competidorEntra){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor no existe'));
                }
                if ($equipo->isReemplazado($competidorEntra)){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor ya fue reemplazo en este equipo'));
                }
                if ($equipoCompetidorSale->getCompetidor() == $competidorEntra){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor que entra no puede ser igual al competidor que sale.'));
                }
                $equipoCompetidorEntra = $equipo->isSustituto($competidorEntra);
                if (!$equipoCompetidorEntra){
                    //No es un sustituto por lo que tiene que ser un reemplazo
                    $equipoCompetidorEntra = $equipo->isReemplazo($competidorEntra);
                    if (!$equipoCompetidorEntra){
                        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El competidor no es un sustituto/reemplazo posible en este equipo'));
                    }
                }
                $equipoCompetidorSale->setEntra($equipoCompetidorEntra);
                $equipoCompetidorEntra->setSale($equipoCompetidorSale);
                $equipoCompetidorSale->setUpdatedBy($this->getUser());
                $em->flush();
                return new JsonResponse(array('success' => true, 'error' => false, 'message' => 'La sustitución fue compeltada!.'));
            }catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => $e->getMessage()));
            }
        }

        return array(
            'equipoCompetidor' => $equipoCompetidorSale
        );

    }
}
