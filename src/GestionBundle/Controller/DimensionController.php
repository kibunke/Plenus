<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Dimensiones controller.
 *
 * @Route("/gestion")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DimensionController extends Controller
{
   /**
     * Lists all Dimensiones entities.
     *
     * @Route("/dimension", name="dimension")
     * @Method("GET")
     * @Template("GestionBundle:Dimension:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $torneos = $em->getRepository('ResultadoBundle:Torneo')->findAll();
        $categorias = $em->getRepository('ResultadoBundle:Categoria')->findAll();
        $generos = $em->getRepository('ResultadoBundle:Genero')->findAll();
        $modalidades = $em->getRepository('ResultadoBundle:Modalidad')->findAll();
        
        return array(
            'torneos' => $torneos,
            'categorias' => $categorias,
            'generos' => $generos,
            'modalidades' => $modalidades,
        );
    }
}
