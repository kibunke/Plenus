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
     * @Template()
     */
    public function showAction(Request $request, Equipo $equipo)
    {
        return array(
            'equipo' => $equipo
        );

    }
}
