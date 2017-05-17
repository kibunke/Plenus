<?php

namespace InscripcionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\PlanillaEstado;
use ResultadoBundle\Entity\Competidor;
/**
 * ActividadesSospechosas controller.
 *
 * @Route("/inscripcion/actividadesSospechosas")
 * @Security("has_role('ROLE_INSCRIPCION_ACTIVIDADES_SOSPECHOSAS')")
 */
class ActividadesSospechosasController extends Controller
{
}
