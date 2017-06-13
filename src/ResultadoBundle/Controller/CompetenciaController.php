<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Etapa;
use ResultadoBundle\Entity\Competencia;
use ResultadoBundle\Entity\CompetenciaOrden;

/**
 * Competencia controller.
 *
 * @Route("/resultados/competencia")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaController extends Controller
{        
    /**
     * @Route("/{id}/new", name="resultado_competencia_new", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template("ResultadoBundle:Competencia:new.html.twig")
     */
    public function newAction(Request $request, Etapa $etapa)
    {
        $formOrden              =  $this->createCompetenciaOrdenForm($etapa);
        $formLiga               =  $this->createCompetenciaLigaForm($etapa);
        $formEliminacionDirecta =  $this->createCompetenciaEliminacionDirectaForm($etapa);
        $formSerie              =  $this->createCompetenciaSerieForm($etapa);
        
        return array(
                        'etapa'                  => $etapa,
                        'formOrden'              => $formOrden->createView(),
                        'formLiga'               => $formLiga->createView(),
                        'formSerie'              => $formSerie->createView(),
                        'formEliminacionDirecta' => $formEliminacionDirecta->createView()
                    );
    }
    
    /**
     * Creates a form to create a CompetenciaOrden entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaOrdenForm(Etapa $etapa)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_competenciaOrden_create', array('id' => $etapa->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a form to create a CompetenciaLiga entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaLigaForm(Etapa $etapa)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_competenciaLiga_create', array('id' => $etapa->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a form to create a CompetenciaEliminacionDirectaPuntos entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaEliminacionDirectaForm(Etapa $etapa)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_competenciaEliminacionDirecta_create', array('id' => $etapa->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaSerieForm(Etapa $etapa)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_competenciaSerie_create', array('id' => $etapa->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }    
}
