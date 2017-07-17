<?php

namespace SorteoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ActividadesSospechosas controller.
 *
 * @Route("/resultados/actividadesSospechosas")
 * @Security("has_role('ROLE_SORTEO_ACTIVIDADES_SOSPECHOSAS')")
 */
class ActividadesSospechosasController extends Controller
{        
    /**
     * Displays a index ActividadesSospechosas.
     *
     * @Route("/", name="resultado_actividades_sospechosas")
     * @Method({"GET","POST"})
     * @Template("SorteoBundle:ActividadesSospechosas:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nomDpl = [];
        $dniDpl = [];
        $dniMen1M = [];
        $dniMay99M = [];
        $form = $this->createFiltroActividadesSospechosasForm();
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($form->getData()['nombreDpl']){
                $nomDpl = $em->getRepository('ResultadoBundle:Participante')->getNombresDpl();
            }
            if ($form->getData()['dniMay99M']){
                $dniMay99M = $em->getRepository('ResultadoBundle:Participante')->getDniMay99M();
            }
            if ($form->getData()['dniMen1M']){
                $dniMen1M = $em->getRepository('ResultadoBundle:Participante')->getDniMen1M();
            }
        }
        return array(
            'formFiltro' => $form->createView(),
            'nomDpl' => $nomDpl,
            'dniDpl' => $dniDpl,
            'dniMay99M' => $dniMay99M,
            'dniMen1M' => $dniMen1M
        );
    }
    
    /**
     * Creates a form to consult.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFiltroActividadesSospechosasForm()
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_actividades_sospechosas'))
                    ->setMethod('POST')
                    ->add('dniDpl', 'checkbox', array(
                                    'label'    => 'Buscar DNI duplicados',
                                    "data" => true,
                                    'required' => false,
                    ))
                    ->add('dniMay99M', 'checkbox', array(
                                    'label'    => 'Buscar DNI mayores a 99 millones',
                                    "data" => true,
                                    'required' => false,
                    ))
                    ->add('dniMen1M', 'checkbox', array(
                                    'label'    => 'Buscar DNI menores a 1 millon',
                                    "data" => true,
                                    'required' => false,
                    ))
                    ->add('nombreDpl', 'checkbox', array(
                                    'label'    => 'Buscar nombres duplicados',
                                    "data" => true,
                                    'required' => false,
                    ))
                    ->getForm();
        ;
    }
}
