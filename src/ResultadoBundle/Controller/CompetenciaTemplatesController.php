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
use ResultadoBundle\Entity\EtapaClasificacion;
use ResultadoBundle\Entity\EtapaFinal;
use ResultadoBundle\Entity\EtapaMedallero;
use ResultadoBundle\Entity\Zona;
use ResultadoBundle\Entity\Serie;
use ResultadoBundle\Entity\Plaza;
use ResultadoBundle\Entity\PlazaZona;
use ResultadoBundle\Entity\PlazaCopa;
use ResultadoBundle\Entity\PlazaSerie;

/**
 * CompetenciaLiga controller.
 *
 * @Route("/resultados/competencia/template")
 * @Security("has_role('ROLE_COMPETENCIA')")
 */
class CompetenciaTemplatesController extends Controller
{
    /**
     * Displays a view to create a new Competencia entity.
     *
     * @Route("/{id}/new", name="resultado_competencia_template")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template("ResultadoBundle:Competencia:template.html.twig")
     */
    public function newTemplatesAction(Request $request, Evento $evento)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        /* TEMPLATES */
        $template4x3 =  $this->createCompetenciaLigaTemplate4x3ActionForm($evento);
        $template4x6 =  $this->createCompetenciaLigaTemplate4x6ActionForm($evento);
        $template2x6 =  $this->createCompetenciaLigaTemplate2x6ActionForm($evento);
        $template8x3 =  $this->createCompetenciaLigaTemplate8x3ActionForm($evento);
        $templateSerieFinalVacia = $this->createCompetenciaSerieFinalVaciaTemplateActionForm($evento);
        
        $templateSerieFinalx12 = $this->createCompetenciaSerieFinalx12TemplateActionForm($evento);
        $templateSerie2x6 = $this->createCompetenciaSerie2x6TemplateActionForm($evento);
        
        return array(
            'evento' => $evento,
            'template4x3'=>$template4x3->createView(),
            'template4x6'=>$template4x6->createView(),
            'template2x6'=>$template2x6->createView(),
            'template8x3'=>$template8x3->createView(),
            'templateSerieFinalVacia'=>$templateSerieFinalVacia->createView(),
            'templateSerieFinalx12'=>$templateSerieFinalx12->createView(),
            'templateSerie2x6'=>$templateSerie2x6->createView()
        );
    }
    
    /**
     * Creates a new CompetenciaLiga entities by etapa.
     *
     * @Route("/{id}/liga/4x3/new", name="resultado_CompetenciaLigaTemplate4x3")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaLigaTemplate4x3Action(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaLigaTemplate4x3ActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaClasificacion = new EtapaClasificacion($this->getUser());
            $etapaClasificacion->setNombre("Etapa de Clasificación");
            $etapaClasificacion->setEvento($evento);
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            /* ETAPA DE CLASIFICACION */
            $tipo = "ResultadoBundle\Entity\CompetenciaLiga".$disciplinaParam->tipo;
            $competenciaClasificacion = new $tipo($this->getUser());
            $competenciaClasificacion->setNombre("Competencia para ".$etapaClasificacion->getNombre());
            /* ZONA A */
            $zonaA = new Zona($this->getUser(),"Zona A");
            $plaza1 = new PlazaZona($this->getUser(),"Equipo 1");
            $plaza1->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza1);
            $plaza2 = new PlazaZona($this->getUser(),"Equipo 2");
            $plaza2->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza2);
            $plaza3 = new PlazaZona($this->getUser(),"Equipo 3");
            $plaza3->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza3);
            /* Partido 1 - ZONA A */
            $partido1 = $competenciaClasificacion->newPartido($this->getUser(),"1");
            $partido1->setPlaza1($plaza1);
            $partido1->setPlaza2($plaza2);
            $zonaA->addPartido($partido1);
            /* Partido 5 - ZONA 1*/
            $partido5 = $competenciaClasificacion->newPartido($this->getUser(),"5");
            $partido5->setPlaza1($plaza1);
            $partido5->setPlaza2($plaza3);
            $zonaA->addPartido($partido5);
            /* Partido 9 - ZONA A*/
            $partido9 = $competenciaClasificacion->newPartido($this->getUser(),"9");
            $partido9->setPlaza1($plaza2);
            $partido9->setPlaza2($plaza3);
            $zonaA->addPartido($partido9);
            $competenciaClasificacion->addZona($zonaA);
            
            /* ZONA B */
            $zonaB = new Zona($this->getUser(),"Zona B");
            $plaza4 = new PlazaZona($this->getUser(),"Equipo 4");
            $plaza4->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza4);
            $plaza5 = new PlazaZona($this->getUser(),"Equipo 5");
            $plaza5->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza5);
            $plaza6 = new PlazaZona($this->getUser(),"Equipo 6");
            $plaza6->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza6);
            /* Partido 1 - ZONA B */
            $partido2 = $competenciaClasificacion->newPartido($this->getUser(),"2");
            $partido2->setPlaza1($plaza4);
            $partido2->setPlaza2($plaza5);
            $zonaB->addPartido($partido2);
            /* Partido 5 - ZONA B */
            $partido6 = $competenciaClasificacion->newPartido($this->getUser(),"6");
            $partido6->setPlaza1($plaza4);
            $partido6->setPlaza2($plaza6);
            $zonaB->addPartido($partido6);
            /* Partido 9 - ZONA B */
            $partido10 = $competenciaClasificacion->newPartido($this->getUser(),"10");
            $partido10->setPlaza1($plaza5);
            $partido10->setPlaza2($plaza6);
            $zonaB->addPartido($partido10);
            $competenciaClasificacion->addZona($zonaB);
            
            /* ZONA C */
            $zonaC = new Zona($this->getUser(),"Zona C");
            $plaza7 = new PlazaZona($this->getUser(),"Equipo 7");
            $plaza7->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza7);
            $plaza8 = new PlazaZona($this->getUser(),"Equipo 8");
            $plaza8->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza8);
            $plaza9 = new PlazaZona($this->getUser(),"Equipo 9");
            $plaza9->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza9);
            /* Partido 3 - ZONA C */
            $partido3 = $competenciaClasificacion->newPartido($this->getUser(),"3");
            $partido3->setPlaza1($plaza7);
            $partido3->setPlaza2($plaza8);
            $zonaC->addPartido($partido3);
            /* Partido 7 - ZONA C */
            $partido7 = $competenciaClasificacion->newPartido($this->getUser(),"7");
            $partido7->setPlaza1($plaza7);
            $partido7->setPlaza2($plaza9);
            $zonaC->addPartido($partido7);
            /* Partido 11 - ZONA C */
            $partido11 = $competenciaClasificacion->newPartido($this->getUser(),"11");
            $partido11->setPlaza1($plaza8);
            $partido11->setPlaza2($plaza9);
            $zonaC->addPartido($partido11);
            $competenciaClasificacion->addZona($zonaC);
            
            /* ZONA D */
            $zonaD = new Zona($this->getUser(),"Zona D");
            $plaza10 = new PlazaZona($this->getUser(),"Equipo 10");
            $plaza10->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza10);
            $plaza11 = new PlazaZona($this->getUser(),"Equipo 11");
            $plaza11->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza11);
            $plaza12 = new PlazaZona($this->getUser(),"Equipo 12");
            $plaza12->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza12);
            /* Partido 4 - ZONA D */
            $partido4 = $competenciaClasificacion->newPartido($this->getUser(),"4");
            $partido4->setPlaza1($plaza10);
            $partido4->setPlaza2($plaza11);
            $zonaD->addPartido($partido4);
            /* Partido 8 - ZONA D */
            $partido8 = $competenciaClasificacion->newPartido($this->getUser(),"8");
            $partido8->setPlaza1($plaza10);
            $partido8->setPlaza2($plaza12);
            $zonaD->addPartido($partido8);
            /* Partido 12 - ZONA D */
            $partido12 = $competenciaClasificacion->newPartido($this->getUser(),"12");
            $partido12->setPlaza1($plaza11);
            $partido12->setPlaza2($plaza12);
            $zonaD->addPartido($partido12);
            $competenciaClasificacion->addZona($zonaD);            
            
            $etapaClasificacion->setCompetencia($competenciaClasificacion);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaEliminacionDirecta".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $numPartido = 13;
            
            $partidoSemi1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi1->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona A",99,$competenciaFinal));
            $partidoSemi1->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona C",99,$competenciaFinal));
            $partidoSemi1->setNivel(2);
            $partidoSemi1->setOrden(1);
            $competenciaFinal->addPartido($partidoSemi1);
            $numPartido++;

            $partidoSemi2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi2->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona B",99,$competenciaFinal));
            $partidoSemi2->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona D",99,$competenciaFinal));            
            $partidoSemi2->setNivel(2);
            $partidoSemi2->setOrden(2);
            $competenciaFinal->addPartido($partidoSemi2);
            $numPartido++;
            
            if ($form->getData()['tercero']){
                $partido3ro = $competenciaFinal->newPartido($this->getUser(),$numPartido);
                $partido3ro->setPlaza1(new PlazaCopa($this->getUser(),"Perdedor Partido 13",99,$competenciaFinal));
                $partido3ro->setPlaza2(new PlazaCopa($this->getUser(),"Perdedor Partido 14",99,$competenciaFinal));                
                $partido3ro->setNivel(0);
                $partido3ro->setOrden(1);
                $competenciaFinal->addPartido($partido3ro);
                $numPartido++;
            }else{
                $medallero->getCompetencia()->addPlazas(new Plaza($this->getUser(),"Medalla de Bronce",3));
            }
            
            $partidoFinal = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoFinal->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 13",99,$competenciaFinal));
            $partidoFinal->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 14",99,$competenciaFinal));                            
            $partidoFinal->setNivel(1);
            $partidoFinal->setOrden(1);
            $competenciaFinal->addPartido($partidoFinal);
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaClasificacion);
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaLigaTemplate4x3ActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaLigaTemplate4x3', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->add('tercero', 'checkbox', array(
                                    'label'    => 'Juega 3er puesto ?',
                                    "data" => true,
                                    'required' => false,
                    ))                    
                    ->getForm();
        ;
    }
    
    
    /**
     * Creates a new CompetenciaLiga entities by etapa.
     *
     * @Route("/{id}/liga/4x6/new", name="resultado_CompetenciaLigaTemplate4x6")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaLigaTemplate4x6Action(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaLigaTemplate4x6ActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaClasificacion = new EtapaClasificacion($this->getUser());
            $etapaClasificacion->setNombre("Etapa de Clasificación");
            $etapaClasificacion->setEvento($evento);
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            /* ETAPA DE CLASIFICACION */
            $tipo = "ResultadoBundle\Entity\CompetenciaLiga".$disciplinaParam->tipo;
            $competenciaClasificacion = new $tipo($this->getUser());
            $competenciaClasificacion->setNombre("Competencia para ".$etapaClasificacion->getNombre());
            /* ZONA A */
            $zonaA = new Zona($this->getUser(),"Zona A");
            $plaza1 = new PlazaZona($this->getUser(),"Equipo 1");
            $plaza1->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza1);
            $plaza2 = new PlazaZona($this->getUser(),"Equipo 2");
            $plaza2->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza2);
            $plaza3 = new PlazaZona($this->getUser(),"Equipo 3");
            $plaza3->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza3);
            $plaza4 = new PlazaZona($this->getUser(),"Equipo 4");
            $plaza4->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza4);
            $plaza5 = new PlazaZona($this->getUser(),"Equipo 5");
            $plaza5->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza5);
            $plaza6 = new PlazaZona($this->getUser(),"Equipo 6");
            $plaza6->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza6);
            /* Partido 1 - ZONA A */
            $partido1 = $competenciaClasificacion->newPartido($this->getUser(),"1");
            $partido1->setPlaza1($plaza1);
            $partido1->setPlaza2($plaza2);
            $zonaA->addPartido($partido1);
            /* Partido 3 - ZONA A*/
            $partido3 = $competenciaClasificacion->newPartido($this->getUser(),"3");
            $partido3->setPlaza1($plaza3);
            $partido3->setPlaza2($plaza4);
            $zonaA->addPartido($partido3);
            /* Partido 5 - ZONA A*/
            $partido5 = $competenciaClasificacion->newPartido($this->getUser(),"5");
            $partido5->setPlaza1($plaza5);
            $partido5->setPlaza2($plaza6);
            $zonaA->addPartido($partido5);

            /* Partido 13 - ZONA A*/
            $partido13 = $competenciaClasificacion->newPartido($this->getUser(),"13");
            $partido13->setPlaza1($plaza1);
            $partido13->setPlaza2($plaza3);
            $zonaA->addPartido($partido13);
            /* Partido 15 - ZONA A*/
            $partido15 = $competenciaClasificacion->newPartido($this->getUser(),"15");
            $partido15->setPlaza1($plaza2);
            $partido15->setPlaza2($plaza5);
            $zonaA->addPartido($partido15);
            /* Partido 17 - ZONA A*/
            $partido17 = $competenciaClasificacion->newPartido($this->getUser(),"17");
            $partido17->setPlaza1($plaza4);
            $partido17->setPlaza2($plaza6);
            $zonaA->addPartido($partido17);
            
            /* Partido 25 - ZONA A*/
            $partido25 = $competenciaClasificacion->newPartido($this->getUser(),"25");
            $partido25->setPlaza1($plaza1);
            $partido25->setPlaza2($plaza4);
            $zonaA->addPartido($partido25);
            /* Partido 27 - ZONA A*/
            $partido27 = $competenciaClasificacion->newPartido($this->getUser(),"27");
            $partido27->setPlaza1($plaza2);
            $partido27->setPlaza2($plaza6);
            $zonaA->addPartido($partido27);
            /* Partido 29 - ZONA A*/
            $partido29 = $competenciaClasificacion->newPartido($this->getUser(),"29");
            $partido29->setPlaza1($plaza3);
            $partido29->setPlaza2($plaza5);
            $zonaA->addPartido($partido29);
            
            /* Partido 37 - ZONA A*/
            $partido37 = $competenciaClasificacion->newPartido($this->getUser(),"37");
            $partido37->setPlaza1($plaza1);
            $partido37->setPlaza2($plaza6);
            $zonaA->addPartido($partido37);
            /* Partido 39 - ZONA A*/
            $partido39 = $competenciaClasificacion->newPartido($this->getUser(),"39");
            $partido39->setPlaza1($plaza2);
            $partido39->setPlaza2($plaza3);
            $zonaA->addPartido($partido39);
            /* Partido 41 - ZONA A*/
            $partido41 = $competenciaClasificacion->newPartido($this->getUser(),"41");
            $partido41->setPlaza1($plaza4);
            $partido41->setPlaza2($plaza5);
            $zonaA->addPartido($partido41);
            
            /* Partido 49 - ZONA A*/
            $partido49 = $competenciaClasificacion->newPartido($this->getUser(),"49");
            $partido49->setPlaza1($plaza1);
            $partido49->setPlaza2($plaza5);
            $zonaA->addPartido($partido49);
            /* Partido 51 - ZONA A*/
            $partido51 = $competenciaClasificacion->newPartido($this->getUser(),"51");
            $partido51->setPlaza1($plaza2);
            $partido51->setPlaza2($plaza4);
            $zonaA->addPartido($partido51);            
            /* Partido 53 - ZONA A*/
            $partido53 = $competenciaClasificacion->newPartido($this->getUser(),"53");
            $partido53->setPlaza1($plaza3);
            $partido53->setPlaza2($plaza6);
            $zonaA->addPartido($partido53);
            
            $competenciaClasificacion->addZona($zonaA);
            
            /* ZONA B */
            $zonaB = new Zona($this->getUser(),"Zona B");
            $plaza7 = new PlazaZona($this->getUser(),"Equipo 7");
            $plaza7->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza7);
            $plaza8 = new PlazaZona($this->getUser(),"Equipo 8");
            $plaza8->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza8);
            $plaza9 = new PlazaZona($this->getUser(),"Equipo 9");
            $plaza9->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza9);
            $plaza10 = new PlazaZona($this->getUser(),"Equipo 10");
            $plaza10->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza10);
            $plaza11 = new PlazaZona($this->getUser(),"Equipo 11");
            $plaza11->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza11);
            $plaza12 = new PlazaZona($this->getUser(),"Equipo 12");
            $plaza12->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza12);
            
            /* Partido 7 - ZONA B */
            $partido7 = $competenciaClasificacion->newPartido($this->getUser(),"7");
            $partido7->setPlaza1($plaza7);
            $partido7->setPlaza2($plaza8);
            $zonaB->addPartido($partido7);
            /* Partido 9 - ZONA B */
            $partido9 = $competenciaClasificacion->newPartido($this->getUser(),"9");
            $partido9->setPlaza1($plaza9);
            $partido9->setPlaza2($plaza10);
            $zonaB->addPartido($partido9);
            /* Partido 11 - ZONA B */
            $partido11 = $competenciaClasificacion->newPartido($this->getUser(),"11");
            $partido11->setPlaza1($plaza11);
            $partido11->setPlaza2($plaza12);
            $zonaB->addPartido($partido11);
            
            /* Partido 19 - ZONA B */
            $partido19 = $competenciaClasificacion->newPartido($this->getUser(),"19");
            $partido19->setPlaza1($plaza7);
            $partido19->setPlaza2($plaza9);
            $zonaB->addPartido($partido19);
            /* Partido 21 - ZONA B */
            $partido21 = $competenciaClasificacion->newPartido($this->getUser(),"21");
            $partido21->setPlaza1($plaza8);
            $partido21->setPlaza2($plaza11);
            $zonaB->addPartido($partido21);
            /* Partido 23 - ZONA B */
            $partido23 = $competenciaClasificacion->newPartido($this->getUser(),"23");
            $partido23->setPlaza1($plaza10);
            $partido23->setPlaza2($plaza12);
            $zonaB->addPartido($partido23);
            
            /* Partido 31 - ZONA B */
            $partido31 = $competenciaClasificacion->newPartido($this->getUser(),"31");
            $partido31->setPlaza1($plaza7);
            $partido31->setPlaza2($plaza10);
            $zonaB->addPartido($partido31);
            /* Partido 33 - ZONA B */
            $partido33 = $competenciaClasificacion->newPartido($this->getUser(),"33");
            $partido33->setPlaza1($plaza8);
            $partido33->setPlaza2($plaza12);
            $zonaB->addPartido($partido33);
            /* Partido 35 - ZONA B */
            $partido35 = $competenciaClasificacion->newPartido($this->getUser(),"35");
            $partido35->setPlaza1($plaza9);
            $partido35->setPlaza2($plaza11);
            $zonaB->addPartido($partido35);
            
            /* Partido 43 - ZONA B */
            $partido43 = $competenciaClasificacion->newPartido($this->getUser(),"43");
            $partido43->setPlaza1($plaza7);
            $partido43->setPlaza2($plaza12);
            $zonaB->addPartido($partido43);
            /* Partido 45 - ZONA B */
            $partido45 = $competenciaClasificacion->newPartido($this->getUser(),"45");
            $partido45->setPlaza1($plaza8);
            $partido45->setPlaza2($plaza9);
            $zonaB->addPartido($partido45);
            /* Partido 47 - ZONA B */
            $partido47 = $competenciaClasificacion->newPartido($this->getUser(),"47");
            $partido47->setPlaza1($plaza10);
            $partido47->setPlaza2($plaza11);
            $zonaB->addPartido($partido47);
            
            /* Partido 55 - ZONA B */
            $partido55 = $competenciaClasificacion->newPartido($this->getUser(),"55");
            $partido55->setPlaza1($plaza7);
            $partido55->setPlaza2($plaza11);
            $zonaB->addPartido($partido55);
            /* Partido 57 - ZONA B */
            $partido57 = $competenciaClasificacion->newPartido($this->getUser(),"57");
            $partido57->setPlaza1($plaza8);
            $partido57->setPlaza2($plaza10);
            $zonaB->addPartido($partido57);
            /* Partido 59 - ZONA B */
            $partido59 = $competenciaClasificacion->newPartido($this->getUser(),"59");
            $partido59->setPlaza1($plaza9);
            $partido59->setPlaza2($plaza12);
            $zonaB->addPartido($partido59);

            $competenciaClasificacion->addZona($zonaB);

            /* ZONA C */
            $zonaC = new Zona($this->getUser(),"Zona C");
            $plaza13 = new PlazaZona($this->getUser(),"Equipo 13");
            $plaza13->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza13);
            $plaza14 = new PlazaZona($this->getUser(),"Equipo 14");
            $plaza14->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza14);
            $plaza15 = new PlazaZona($this->getUser(),"Equipo 15");
            $plaza15->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza15);
            $plaza16 = new PlazaZona($this->getUser(),"Equipo 16");
            $plaza16->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza16);
            $plaza17 = new PlazaZona($this->getUser(),"Equipo 17");
            $plaza17->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza17);
            $plaza18 = new PlazaZona($this->getUser(),"Equipo 18");
            $plaza18->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza18);            

            /* Partido 2 - ZONA C */
            $partido2 = $competenciaClasificacion->newPartido($this->getUser(),"2");
            $partido2->setPlaza1($plaza13);
            $partido2->setPlaza2($plaza14);
            $zonaC->addPartido($partido2);
            /* Partido 4 - ZONA C */
            $partido4 = $competenciaClasificacion->newPartido($this->getUser(),"4");
            $partido4->setPlaza1($plaza15);
            $partido4->setPlaza2($plaza16);
            $zonaC->addPartido($partido4);
            /* Partido 6 - ZONA C */
            $partido6 = $competenciaClasificacion->newPartido($this->getUser(),"6");
            $partido6->setPlaza1($plaza17);
            $partido6->setPlaza2($plaza18);
            $zonaC->addPartido($partido6);
            
            /* Partido 14 - ZONA C */
            $partido14 = $competenciaClasificacion->newPartido($this->getUser(),"14");
            $partido14->setPlaza1($plaza13);
            $partido14->setPlaza2($plaza15);
            $zonaC->addPartido($partido14);
            /* Partido 15 - ZONA C */
            $partido15 = $competenciaClasificacion->newPartido($this->getUser(),"15");
            $partido15->setPlaza1($plaza14);
            $partido15->setPlaza2($plaza17);
            $zonaC->addPartido($partido15);
            /* Partido 16 - ZONA C */
            $partido16 = $competenciaClasificacion->newPartido($this->getUser(),"16");
            $partido16->setPlaza1($plaza16);
            $partido16->setPlaza2($plaza18);
            $zonaC->addPartido($partido16);

            /* Partido 26 - ZONA C */
            $partido26 = $competenciaClasificacion->newPartido($this->getUser(),"26");
            $partido26->setPlaza1($plaza13);
            $partido26->setPlaza2($plaza16);
            $zonaC->addPartido($partido26);
            /* Partido 28 - ZONA C */
            $partido28 = $competenciaClasificacion->newPartido($this->getUser(),"28");
            $partido28->setPlaza1($plaza14);
            $partido28->setPlaza2($plaza18);
            $zonaC->addPartido($partido28);
            /* Partido 30 - ZONA C */
            $partido30 = $competenciaClasificacion->newPartido($this->getUser(),"30");
            $partido30->setPlaza1($plaza15);
            $partido30->setPlaza2($plaza17);
            $zonaC->addPartido($partido30);            

            /* Partido 38 - ZONA C */
            $partido38 = $competenciaClasificacion->newPartido($this->getUser(),"38");
            $partido38->setPlaza1($plaza13);
            $partido38->setPlaza2($plaza18);
            $zonaC->addPartido($partido38);
            /* Partido 40 - ZONA C */
            $partido40 = $competenciaClasificacion->newPartido($this->getUser(),"40");
            $partido40->setPlaza1($plaza14);
            $partido40->setPlaza2($plaza15);
            $zonaC->addPartido($partido40);
            /* Partido 42 - ZONA C */
            $partido42 = $competenciaClasificacion->newPartido($this->getUser(),"42");
            $partido42->setPlaza1($plaza16);
            $partido42->setPlaza2($plaza17);
            $zonaC->addPartido($partido42);
            
            /* Partido 50 - ZONA C */
            $partido50 = $competenciaClasificacion->newPartido($this->getUser(),"50");
            $partido50->setPlaza1($plaza13);
            $partido50->setPlaza2($plaza17);
            $zonaC->addPartido($partido50);
            /* Partido 52 - ZONA C */
            $partido52 = $competenciaClasificacion->newPartido($this->getUser(),"52");
            $partido52->setPlaza1($plaza14);
            $partido52->setPlaza2($plaza16);
            $zonaC->addPartido($partido52);
            /* Partido 54 - ZONA C */
            $partido54 = $competenciaClasificacion->newPartido($this->getUser(),"54");
            $partido54->setPlaza1($plaza15);
            $partido54->setPlaza2($plaza18);
            $zonaC->addPartido($partido54);            
            
            $competenciaClasificacion->addZona($zonaC);
            
            /* ZONA D */
            $zonaD = new Zona($this->getUser(),"Zona D");
            $plaza19 = new PlazaZona($this->getUser(),"Equipo 19");
            $plaza19->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza19);
            $plaza20 = new PlazaZona($this->getUser(),"Equipo 20");
            $plaza20->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza20);
            $plaza21 = new PlazaZona($this->getUser(),"Equipo 21");
            $plaza21->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza21);
            $plaza22 = new PlazaZona($this->getUser(),"Equipo 22");
            $plaza22->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza22);
            $plaza23 = new PlazaZona($this->getUser(),"Equipo 23");
            $plaza23->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza23);
            $plaza24 = new PlazaZona($this->getUser(),"Equipo 24");
            $plaza24->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza24);
            
            /* Partido 8 - ZONA D */
            $partido8 = $competenciaClasificacion->newPartido($this->getUser(),"8");
            $partido8->setPlaza1($plaza19);
            $partido8->setPlaza2($plaza20);
            $zonaD->addPartido($partido8);
            /* Partido 10 - ZONA D */
            $partido10 = $competenciaClasificacion->newPartido($this->getUser(),"10");
            $partido10->setPlaza1($plaza21);
            $partido10->setPlaza2($plaza22);
            $zonaD->addPartido($partido10);
            /* Partido 12 - ZONA D */
            $partido12 = $competenciaClasificacion->newPartido($this->getUser(),"12");
            $partido12->setPlaza1($plaza23);
            $partido12->setPlaza2($plaza24);
            $zonaD->addPartido($partido12);
            
            /* Partido 20 - ZONA D */
            $partido20 = $competenciaClasificacion->newPartido($this->getUser(),"20");
            $partido20->setPlaza1($plaza19);
            $partido20->setPlaza2($plaza21);
            $zonaD->addPartido($partido20);
            /* Partido 22 - ZONA D */
            $partido22 = $competenciaClasificacion->newPartido($this->getUser(),"22");
            $partido22->setPlaza1($plaza20);
            $partido22->setPlaza2($plaza23);
            $zonaD->addPartido($partido22);
            /* Partido 24 - ZONA D */
            $partido24 = $competenciaClasificacion->newPartido($this->getUser(),"24");
            $partido24->setPlaza1($plaza22);
            $partido24->setPlaza2($plaza24);
            $zonaD->addPartido($partido24);
            
            /* Partido 32 - ZONA D */
            $partido32 = $competenciaClasificacion->newPartido($this->getUser(),"32");
            $partido32->setPlaza1($plaza19);
            $partido32->setPlaza2($plaza22);
            $zonaD->addPartido($partido32);
            /* Partido 34 - ZONA D */
            $partido34 = $competenciaClasificacion->newPartido($this->getUser(),"34");
            $partido34->setPlaza1($plaza20);
            $partido34->setPlaza2($plaza24);
            $zonaD->addPartido($partido34);
            /* Partido 36 - ZONA D */
            $partido36 = $competenciaClasificacion->newPartido($this->getUser(),"36");
            $partido36->setPlaza1($plaza21);
            $partido36->setPlaza2($plaza23);
            $zonaD->addPartido($partido36);
            
            /* Partido 44 - ZONA D */
            $partido44 = $competenciaClasificacion->newPartido($this->getUser(),"44");
            $partido44->setPlaza1($plaza19);
            $partido44->setPlaza2($plaza24);
            $zonaD->addPartido($partido44);
            /* Partido 46 - ZONA D */
            $partido46 = $competenciaClasificacion->newPartido($this->getUser(),"46");
            $partido46->setPlaza1($plaza20);
            $partido46->setPlaza2($plaza21);
            $zonaD->addPartido($partido46);
            /* Partido 48 - ZONA D */
            $partido48 = $competenciaClasificacion->newPartido($this->getUser(),"48");
            $partido48->setPlaza1($plaza22);
            $partido48->setPlaza2($plaza23);
            $zonaD->addPartido($partido48);
            
            /* Partido 56 - ZONA D */
            $partido56 = $competenciaClasificacion->newPartido($this->getUser(),"56");
            $partido56->setPlaza1($plaza19);
            $partido56->setPlaza2($plaza23);
            $zonaD->addPartido($partido56);
            /* Partido 58 - ZONA D */
            $partido58 = $competenciaClasificacion->newPartido($this->getUser(),"58");
            $partido58->setPlaza1($plaza20);
            $partido58->setPlaza2($plaza22);
            $zonaD->addPartido($partido58);
            /* Partido 60 - ZONA D */
            $partido60 = $competenciaClasificacion->newPartido($this->getUser(),"60");
            $partido60->setPlaza1($plaza21);
            $partido60->setPlaza2($plaza24);
            $zonaD->addPartido($partido60);
            $competenciaClasificacion->addZona($zonaD);            
            
            $etapaClasificacion->setCompetencia($competenciaClasificacion);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaEliminacionDirecta".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $numPartido = 61;
            
            $partido4tos1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos1->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona A",99,$competenciaFinal));
            $partido4tos1->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona C",99,$competenciaFinal));
            $partido4tos1->setNivel(3);
            $partido4tos1->setOrden(1);
            $competenciaFinal->addPartido($partido4tos1);
            $numPartido++;

            $partido4tos2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos2->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona A",99,$competenciaFinal));
            $partido4tos2->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona C",99,$competenciaFinal));
            $partido4tos2->setNivel(3);
            $partido4tos2->setOrden(2);
            $competenciaFinal->addPartido($partido4tos2);
            $numPartido++;            
            
            $partido4tos3 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos3->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona B",99,$competenciaFinal));
            $partido4tos3->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona D",99,$competenciaFinal));
            $partido4tos3->setNivel(3);
            $partido4tos3->setOrden(3);
            $competenciaFinal->addPartido($partido4tos3);
            $numPartido++;
            
            $partido4tos4 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos4->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona B",99,$competenciaFinal));
            $partido4tos4->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona D",99,$competenciaFinal));
            $partido4tos4->setNivel(3);
            $partido4tos4->setOrden(4);
            $competenciaFinal->addPartido($partido4tos4);
            $numPartido++;
            
            $partidoSemi1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi1->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 61",99,$competenciaFinal));
            $partidoSemi1->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 63",99,$competenciaFinal));
            $partidoSemi1->setNivel(2);
            $partidoSemi1->setOrden(1);
            $competenciaFinal->addPartido($partidoSemi1);
            $numPartido++;

            $partidoSemi2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi2->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 62",99,$competenciaFinal));
            $partidoSemi2->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 64",99,$competenciaFinal));            
            $partidoSemi2->setNivel(2);
            $partidoSemi2->setOrden(2);
            $competenciaFinal->addPartido($partidoSemi2);
            $numPartido++;
            
            if ($form->getData()['tercero']){
                $partido3ro = $competenciaFinal->newPartido($this->getUser(),$numPartido);
                $partido3ro->setPlaza1(new PlazaCopa($this->getUser(),"Perdedor Partido 65",99,$competenciaFinal));
                $partido3ro->setPlaza2(new PlazaCopa($this->getUser(),"Perdedor Partido 66",99,$competenciaFinal));                
                $partido3ro->setNivel(0);
                $partido3ro->setOrden(1);
                $competenciaFinal->addPartido($partido3ro);
                $numPartido++;
            }else{
                $medallero->getCompetencia()->addPlazas(new Plaza($this->getUser(),"Medalla de Bronce",3));
            }
            
            $partidoFinal = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoFinal->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 65",99,$competenciaFinal));
            $partidoFinal->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 66",99,$competenciaFinal));                            
            $partidoFinal->setNivel(1);
            $partidoFinal->setOrden(1);
            $competenciaFinal->addPartido($partidoFinal);
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaClasificacion);
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaLigaTemplate4x6ActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaLigaTemplate4x6', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->add('tercero', 'checkbox', array(
                                    'label'    => 'Juega 3er puesto ?',
                                    "data" => true,
                                    'required' => false,
                    ))                    
                    ->getForm();
        ;
    }
    
    /**
     * Creates a new CompetenciaLiga entities by etapa.
     *
     * @Route("/{id}/liga/2x6/new", name="resultado_CompetenciaLigaTemplate2x6")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaLigaTemplate2x6Action(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaLigaTemplate2x6ActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaClasificacion = new EtapaClasificacion($this->getUser());
            $etapaClasificacion->setNombre("Etapa de Clasificación");
            $etapaClasificacion->setEvento($evento);
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            /* ETAPA DE CLASIFICACION */
            $tipo = "ResultadoBundle\Entity\CompetenciaLiga".$disciplinaParam->tipo;
            $competenciaClasificacion = new $tipo($this->getUser());
            $competenciaClasificacion->setNombre("Competencia para ".$etapaClasificacion->getNombre());
            
            /* ZONA A */
            $zonaA = new Zona($this->getUser(),"Zona A");
            $plaza1 = new PlazaZona($this->getUser(),"Equipo 1");
            $plaza1->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza1);
            $plaza2 = new PlazaZona($this->getUser(),"Equipo 2");
            $plaza2->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza2);
            $plaza3 = new PlazaZona($this->getUser(),"Equipo 3");
            $plaza3->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza3);
            $plaza4 = new PlazaZona($this->getUser(),"Equipo 4");
            $plaza4->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza4);
            $plaza5 = new PlazaZona($this->getUser(),"Equipo 5");
            $plaza5->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza5);
            $plaza6 = new PlazaZona($this->getUser(),"Equipo 6");
            $plaza6->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza6);            
            /* Partido 1 - ZONA A */
            $partido1 = $competenciaClasificacion->newPartido($this->getUser(),"1");
            $partido1->setPlaza1($plaza1);
            $partido1->setPlaza2($plaza2);
            $zonaA->addPartido($partido1);
            /* Partido 2 - ZONA A*/
            $partido2 = $competenciaClasificacion->newPartido($this->getUser(),"2");
            $partido2->setPlaza1($plaza3);
            $partido2->setPlaza2($plaza4);
            $zonaA->addPartido($partido2);
            /* Partido 3 - ZONA A*/
            $partido3 = $competenciaClasificacion->newPartido($this->getUser(),"3");
            $partido3->setPlaza1($plaza5);
            $partido3->setPlaza2($plaza6);
            $zonaA->addPartido($partido3);

            /* Partido 7 - ZONA A*/
            $partido7 = $competenciaClasificacion->newPartido($this->getUser(),"7");
            $partido7->setPlaza1($plaza1);
            $partido7->setPlaza2($plaza3);
            $zonaA->addPartido($partido7);
            /* Partido 9 - ZONA A*/
            $partido9 = $competenciaClasificacion->newPartido($this->getUser(),"9");
            $partido9->setPlaza1($plaza2);
            $partido9->setPlaza2($plaza4);
            $zonaA->addPartido($partido9);
            /* Partido 11 - ZONA A*/
            $partido11 = $competenciaClasificacion->newPartido($this->getUser(),"11");
            $partido11->setPlaza1($plaza1);
            $partido11->setPlaza2($plaza5);
            $zonaA->addPartido($partido11);
            
            /* Partido 13 - ZONA A*/
            $partido13 = $competenciaClasificacion->newPartido($this->getUser(),"13");
            $partido13->setPlaza1($plaza3);
            $partido13->setPlaza2($plaza6);
            $zonaA->addPartido($partido13);
            /* Partido 15 - ZONA A*/
            $partido15 = $competenciaClasificacion->newPartido($this->getUser(),"15");
            $partido15->setPlaza1($plaza4);
            $partido15->setPlaza2($plaza5);
            $zonaA->addPartido($partido15);
            /* Partido 17 - ZONA A*/
            $partido17 = $competenciaClasificacion->newPartido($this->getUser(),"17");
            $partido17->setPlaza1($plaza2);
            $partido17->setPlaza2($plaza6);
            $zonaA->addPartido($partido17);
            
            /* Partido 19 - ZONA A*/
            $partido19 = $competenciaClasificacion->newPartido($this->getUser(),"19");
            $partido19->setPlaza1($plaza1);
            $partido19->setPlaza2($plaza4);
            $zonaA->addPartido($partido19);
            /* Partido 21 - ZONA A*/
            $partido21 = $competenciaClasificacion->newPartido($this->getUser(),"21");
            $partido21->setPlaza1($plaza2);
            $partido21->setPlaza2($plaza3);
            $zonaA->addPartido($partido21);
            /* Partido 23 - ZONA A*/
            $partido23 = $competenciaClasificacion->newPartido($this->getUser(),"23");
            $partido23->setPlaza1($plaza4);
            $partido23->setPlaza2($plaza6);
            $zonaA->addPartido($partido23);
            
            /* Partido 25 - ZONA A*/
            $partido25 = $competenciaClasificacion->newPartido($this->getUser(),"25");
            $partido25->setPlaza1($plaza2);
            $partido25->setPlaza2($plaza5);
            $zonaA->addPartido($partido25);
            /* Partido 27 - ZONA A*/
            $partido27 = $competenciaClasificacion->newPartido($this->getUser(),"27");
            $partido27->setPlaza1($plaza1);
            $partido27->setPlaza2($plaza6);
            $zonaA->addPartido($partido27);            
            /* Partido 29 - ZONA A*/
            $partido29 = $competenciaClasificacion->newPartido($this->getUser(),"29");
            $partido29->setPlaza1($plaza3);
            $partido29->setPlaza2($plaza5);
            $zonaA->addPartido($partido29);
            
            $competenciaClasificacion->addZona($zonaA);
            
            /* ZONA B */
            $zonaB = new Zona($this->getUser(),"Zona B");
            $plaza7 = new PlazaZona($this->getUser(),"Equipo 7");
            $plaza7->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza7);
            $plaza8 = new PlazaZona($this->getUser(),"Equipo 8");
            $plaza8->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza8);
            $plaza9 = new PlazaZona($this->getUser(),"Equipo 9");
            $plaza9->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza9);
            $plaza10 = new PlazaZona($this->getUser(),"Equipo 10");
            $plaza10->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza10);
            $plaza11 = new PlazaZona($this->getUser(),"Equipo 11");
            $plaza11->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza11);
            $plaza12 = new PlazaZona($this->getUser(),"Equipo 12");
            $plaza12->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza12);            
            /* Partido 4 - ZONA B */
            $partido4 = $competenciaClasificacion->newPartido($this->getUser(),"4");
            $partido4->setPlaza1($plaza7);
            $partido4->setPlaza2($plaza8);
            $zonaB->addPartido($partido4);
            /* Partido 5 - ZONA B*/
            $partido5 = $competenciaClasificacion->newPartido($this->getUser(),"5");
            $partido5->setPlaza1($plaza9);
            $partido5->setPlaza2($plaza10);
            $zonaB->addPartido($partido5);
            /* Partido 6 - ZONA B*/
            $partido6 = $competenciaClasificacion->newPartido($this->getUser(),"6");
            $partido6->setPlaza1($plaza11);
            $partido6->setPlaza2($plaza12);
            $zonaB->addPartido($partido6);

            /* Partido 8 - ZONA B*/
            $partido8 = $competenciaClasificacion->newPartido($this->getUser(),"8");
            $partido8->setPlaza1($plaza7);
            $partido8->setPlaza2($plaza9);
            $zonaB->addPartido($partido8);
            /* Partido 10 - ZONA B*/
            $partido10 = $competenciaClasificacion->newPartido($this->getUser(),"10");
            $partido10->setPlaza1($plaza8);
            $partido10->setPlaza2($plaza10);
            $zonaB->addPartido($partido10);
            /* Partido 12 - ZONA B*/
            $partido12 = $competenciaClasificacion->newPartido($this->getUser(),"12");
            $partido12->setPlaza1($plaza7);
            $partido12->setPlaza2($plaza11);
            $zonaB->addPartido($partido12);
            
            /* Partido 14 - ZONA B*/
            $partido14 = $competenciaClasificacion->newPartido($this->getUser(),"14");
            $partido14->setPlaza1($plaza9);
            $partido14->setPlaza2($plaza12);
            $zonaB->addPartido($partido14);
            /* Partido 16 - ZONA B*/
            $partido16 = $competenciaClasificacion->newPartido($this->getUser(),"16");
            $partido16->setPlaza1($plaza10);
            $partido16->setPlaza2($plaza11);
            $zonaB->addPartido($partido16);
            /* Partido 18 - ZONA B*/
            $partido18 = $competenciaClasificacion->newPartido($this->getUser(),"18");
            $partido18->setPlaza1($plaza8);
            $partido18->setPlaza2($plaza12);
            $zonaB->addPartido($partido18);
            
            /* Partido 20 - ZONA B*/
            $partido20 = $competenciaClasificacion->newPartido($this->getUser(),"20");
            $partido20->setPlaza1($plaza7);
            $partido20->setPlaza2($plaza10);
            $zonaB->addPartido($partido20);
            /* Partido 22 - ZONA B*/
            $partido22 = $competenciaClasificacion->newPartido($this->getUser(),"22");
            $partido22->setPlaza1($plaza8);
            $partido22->setPlaza2($plaza9);
            $zonaB->addPartido($partido22);
            /* Partido 24 - ZONA B*/
            $partido24 = $competenciaClasificacion->newPartido($this->getUser(),"24");
            $partido24->setPlaza1($plaza10);
            $partido24->setPlaza2($plaza12);
            $zonaB->addPartido($partido24);
            
            /* Partido 26 - ZONA B*/
            $partido26 = $competenciaClasificacion->newPartido($this->getUser(),"26");
            $partido26->setPlaza1($plaza8);
            $partido26->setPlaza2($plaza11);
            $zonaB->addPartido($partido26);
            /* Partido 28 - ZONA B*/
            $partido28 = $competenciaClasificacion->newPartido($this->getUser(),"28");
            $partido28->setPlaza1($plaza7);
            $partido28->setPlaza2($plaza12);
            $zonaB->addPartido($partido28);            
            /* Partido 30 - ZONA B*/
            $partido30 = $competenciaClasificacion->newPartido($this->getUser(),"30");
            $partido30->setPlaza1($plaza9);
            $partido30->setPlaza2($plaza11);
            $zonaB->addPartido($partido30);
            
            $competenciaClasificacion->addZona($zonaB);
            
            $etapaClasificacion->setCompetencia($competenciaClasificacion);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaEliminacionDirecta".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $numPartido = 31;
            
            $partidoSemi1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi1->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona A",99,$competenciaFinal));
            $partidoSemi1->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona B",99,$competenciaFinal));
            $partidoSemi1->setNivel(2);
            $partidoSemi1->setOrden(1);
            $competenciaFinal->addPartido($partidoSemi1);
            $numPartido++;

            $partidoSemi2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi2->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona B",99,$competenciaFinal));
            $partidoSemi2->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona A",99,$competenciaFinal));            
            $partidoSemi2->setNivel(2);
            $partidoSemi2->setOrden(2);
            $competenciaFinal->addPartido($partidoSemi2);
            $numPartido++;
            
            if ($form->getData()['tercero']){
                $partido3ro = $competenciaFinal->newPartido($this->getUser(),$numPartido);
                $partido3ro->setPlaza1(new PlazaCopa($this->getUser(),"Perdedor Partido 31",99,$competenciaFinal));
                $partido3ro->setPlaza2(new PlazaCopa($this->getUser(),"Perdedor Partido 32",99,$competenciaFinal));                
                $partido3ro->setNivel(0);
                $partido3ro->setOrden(1);
                $competenciaFinal->addPartido($partido3ro);
                $numPartido++;
            }else{
                $medallero->getCompetencia()->addPlazas(new Plaza($this->getUser(),"Medalla de Bronce",3));
            }
            
            $partidoFinal = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoFinal->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 31",99,$competenciaFinal));
            $partidoFinal->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 32",99,$competenciaFinal));                            
            $partidoFinal->setNivel(1);
            $partidoFinal->setOrden(1);
            $competenciaFinal->addPartido($partidoFinal);
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaClasificacion);
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaLigaTemplate2x6ActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaLigaTemplate2x6', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->add('tercero', 'checkbox', array(
                                    'label'    => 'Juega 3er puesto ?',
                                    "data" => true,
                                    'required' => false,
                    ))                    
                    ->getForm();
        ;
    }
    
    
    /**
     * Creates a new CompetenciaLiga entities by etapa.
     *
     * @Route("/{id}/liga/8x3/new", name="resultado_CompetenciaLigaTemplate8x3")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaLigaTemplate8x3Action(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaLigaTemplate8x3ActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaClasificacion = new EtapaClasificacion($this->getUser());
            $etapaClasificacion->setNombre("Etapa de Clasificación");
            $etapaClasificacion->setEvento($evento);
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            /* ETAPA DE CLASIFICACION */
            $tipo = "ResultadoBundle\Entity\CompetenciaLiga".$disciplinaParam->tipo;
            $competenciaClasificacion = new $tipo($this->getUser());
            $competenciaClasificacion->setNombre("Competencia para ".$etapaClasificacion->getNombre());
            
            /* ZONA A */
            $zonaA = new Zona($this->getUser(),"Zona A");
            $plaza1 = new PlazaZona($this->getUser(),"Equipo 1");
            $plaza1->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza1);
            $plaza2 = new PlazaZona($this->getUser(),"Equipo 2");
            $plaza2->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza2);
            $plaza3 = new PlazaZona($this->getUser(),"Equipo 3");
            $plaza3->setCompetencia($competenciaClasificacion);
            $zonaA->addPlazas($plaza3);
            /* Partido 1 - ZONA A */
            $partido1 = $competenciaClasificacion->newPartido($this->getUser(),"1");
            $partido1->setPlaza1($plaza1);
            $partido1->setPlaza2($plaza2);
            $zonaA->addPartido($partido1);
            /* Partido 9 - ZONA 1*/
            $partido9 = $competenciaClasificacion->newPartido($this->getUser(),"9");
            $partido9->setPlaza1($plaza1);
            $partido9->setPlaza2($plaza3);
            $zonaA->addPartido($partido9);
            /* Partido 17 - ZONA A*/
            $partido17 = $competenciaClasificacion->newPartido($this->getUser(),"17");
            $partido17->setPlaza1($plaza2);
            $partido17->setPlaza2($plaza3);
            $zonaA->addPartido($partido17);
            $competenciaClasificacion->addZona($zonaA);
            
            /* ZONA B */
            $zonaB = new Zona($this->getUser(),"Zona B");
            $plaza4 = new PlazaZona($this->getUser(),"Equipo 4");
            $plaza4->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza4);
            $plaza5 = new PlazaZona($this->getUser(),"Equipo 5");
            $plaza5->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza5);
            $plaza6 = new PlazaZona($this->getUser(),"Equipo 6");
            $plaza6->setCompetencia($competenciaClasificacion);
            $zonaB->addPlazas($plaza6);
            /* Partido 2 - ZONA B */
            $partido2 = $competenciaClasificacion->newPartido($this->getUser(),"2");
            $partido2->setPlaza1($plaza4);
            $partido2->setPlaza2($plaza5);
            $zonaB->addPartido($partido2);
            /* Partido 10 - ZONA B */
            $partido10 = $competenciaClasificacion->newPartido($this->getUser(),"10");
            $partido10->setPlaza1($plaza4);
            $partido10->setPlaza2($plaza6);
            $zonaB->addPartido($partido10);
            /* Partido 18 - ZONA B */
            $partido18 = $competenciaClasificacion->newPartido($this->getUser(),"18");
            $partido18->setPlaza1($plaza5);
            $partido18->setPlaza2($plaza6);
            $zonaB->addPartido($partido18);
            $competenciaClasificacion->addZona($zonaB);
            
            /* ZONA C */
            $zonaC = new Zona($this->getUser(),"Zona C");
            $plaza7 = new PlazaZona($this->getUser(),"Equipo 7");
            $plaza7->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza7);
            $plaza8 = new PlazaZona($this->getUser(),"Equipo 8");
            $plaza8->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza8);
            $plaza9 = new PlazaZona($this->getUser(),"Equipo 9");
            $plaza9->setCompetencia($competenciaClasificacion);
            $zonaC->addPlazas($plaza9);
            /* Partido 3 - ZONA C */
            $partido3 = $competenciaClasificacion->newPartido($this->getUser(),"3");
            $partido3->setPlaza1($plaza7);
            $partido3->setPlaza2($plaza8);
            $zonaC->addPartido($partido3);
            /* Partido 11 - ZONA C */
            $partido11 = $competenciaClasificacion->newPartido($this->getUser(),"11");
            $partido11->setPlaza1($plaza7);
            $partido11->setPlaza2($plaza9);
            $zonaC->addPartido($partido11);
            /* Partido 19 - ZONA C */
            $partido19 = $competenciaClasificacion->newPartido($this->getUser(),"19");
            $partido19->setPlaza1($plaza8);
            $partido19->setPlaza2($plaza9);
            $zonaC->addPartido($partido19);
            $competenciaClasificacion->addZona($zonaC);
            
            /* ZONA D */
            $zonaD = new Zona($this->getUser(),"Zona D");
            $plaza10 = new PlazaZona($this->getUser(),"Equipo 10");
            $plaza10->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza10);
            $plaza11 = new PlazaZona($this->getUser(),"Equipo 11");
            $plaza11->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza11);
            $plaza12 = new PlazaZona($this->getUser(),"Equipo 12");
            $plaza12->setCompetencia($competenciaClasificacion);
            $zonaD->addPlazas($plaza12);
            /* Partido 4 - ZONA D */
            $partido4 = $competenciaClasificacion->newPartido($this->getUser(),"4");
            $partido4->setPlaza1($plaza10);
            $partido4->setPlaza2($plaza11);
            $zonaD->addPartido($partido4);
            /* Partido 12 - ZONA D */
            $partido12 = $competenciaClasificacion->newPartido($this->getUser(),"12");
            $partido12->setPlaza1($plaza10);
            $partido12->setPlaza2($plaza12);
            $zonaD->addPartido($partido12);
            /* Partido 20 - ZONA D */
            $partido20 = $competenciaClasificacion->newPartido($this->getUser(),"20");
            $partido20->setPlaza1($plaza11);
            $partido20->setPlaza2($plaza12);
            $zonaD->addPartido($partido20);
            $competenciaClasificacion->addZona($zonaD);
            
            
            /* ZONA E */
            $zonaE = new Zona($this->getUser(),"Zona E");
            $plaza13 = new PlazaZona($this->getUser(),"Equipo 13");
            $plaza13->setCompetencia($competenciaClasificacion);
            $zonaE->addPlazas($plaza13);
            $plaza14 = new PlazaZona($this->getUser(),"Equipo 14");
            $plaza14->setCompetencia($competenciaClasificacion);
            $zonaE->addPlazas($plaza14);
            $plaza15 = new PlazaZona($this->getUser(),"Equipo 15");
            $plaza15->setCompetencia($competenciaClasificacion);
            $zonaE->addPlazas($plaza15);
            /* Partido 5 - ZONA E */
            $partido5 = $competenciaClasificacion->newPartido($this->getUser(),"5");
            $partido5->setPlaza1($plaza13);
            $partido5->setPlaza2($plaza14);
            $zonaE->addPartido($partido5);
            /* Partido 13 - ZONA E*/
            $partido13 = $competenciaClasificacion->newPartido($this->getUser(),"13");
            $partido13->setPlaza1($plaza13);
            $partido13->setPlaza2($plaza15);
            $zonaE->addPartido($partido13);
            /* Partido 21 - ZONA E*/
            $partido21 = $competenciaClasificacion->newPartido($this->getUser(),"21");
            $partido21->setPlaza1($plaza14);
            $partido21->setPlaza2($plaza15);
            $zonaE->addPartido($partido21);
            $competenciaClasificacion->addZona($zonaE);
            
            /* ZONA F */
            $zonaF = new Zona($this->getUser(),"Zona F");
            $plaza16 = new PlazaZona($this->getUser(),"Equipo 16");
            $plaza16->setCompetencia($competenciaClasificacion);
            $zonaF->addPlazas($plaza16);
            $plaza17 = new PlazaZona($this->getUser(),"Equipo 17");
            $plaza17->setCompetencia($competenciaClasificacion);
            $zonaF->addPlazas($plaza17);
            $plaza18 = new PlazaZona($this->getUser(),"Equipo 18");
            $plaza18->setCompetencia($competenciaClasificacion);
            $zonaF->addPlazas($plaza18);
            /* Partido 6 - ZONA F */
            $partido6 = $competenciaClasificacion->newPartido($this->getUser(),"6");
            $partido6->setPlaza1($plaza16);
            $partido6->setPlaza2($plaza17);
            $zonaF->addPartido($partido6);
            /* Partido 14 - ZONA F */
            $partido14 = $competenciaClasificacion->newPartido($this->getUser(),"14");
            $partido14->setPlaza1($plaza16);
            $partido14->setPlaza2($plaza18);
            $zonaF->addPartido($partido14);
            /* Partido 22 - ZONA F */
            $partido22 = $competenciaClasificacion->newPartido($this->getUser(),"22");
            $partido22->setPlaza1($plaza17);
            $partido22->setPlaza2($plaza18);
            $zonaF->addPartido($partido22);
            $competenciaClasificacion->addZona($zonaF);
            
            /* ZONA G */
            $zonaG = new Zona($this->getUser(),"Zona G");
            $plaza19 = new PlazaZona($this->getUser(),"Equipo 19");
            $plaza19->setCompetencia($competenciaClasificacion);
            $zonaG->addPlazas($plaza19);
            $plaza20 = new PlazaZona($this->getUser(),"Equipo 20");
            $plaza20->setCompetencia($competenciaClasificacion);
            $zonaG->addPlazas($plaza20);
            $plaza21 = new PlazaZona($this->getUser(),"Equipo 21");
            $plaza21->setCompetencia($competenciaClasificacion);
            $zonaG->addPlazas($plaza21);
            /* Partido 7 - ZONA G */
            $partido7 = $competenciaClasificacion->newPartido($this->getUser(),"7");
            $partido7->setPlaza1($plaza19);
            $partido7->setPlaza2($plaza20);
            $zonaG->addPartido($partido7);
            /* Partido 15 - ZONA G */
            $partido15 = $competenciaClasificacion->newPartido($this->getUser(),"15");
            $partido15->setPlaza1($plaza19);
            $partido15->setPlaza2($plaza21);
            $zonaG->addPartido($partido15);
            /* Partido 23 - ZONA G */
            $partido23 = $competenciaClasificacion->newPartido($this->getUser(),"23");
            $partido23->setPlaza1($plaza20);
            $partido23->setPlaza2($plaza21);
            $zonaG->addPartido($partido23);
            $competenciaClasificacion->addZona($zonaG);
            
            /* ZONA H */
            $zonaH = new Zona($this->getUser(),"Zona H");
            $plaza22 = new PlazaZona($this->getUser(),"Equipo 22");
            $plaza22->setCompetencia($competenciaClasificacion);
            $zonaH->addPlazas($plaza22);
            $plaza23 = new PlazaZona($this->getUser(),"Equipo 23");
            $plaza23->setCompetencia($competenciaClasificacion);
            $zonaH->addPlazas($plaza23);
            $plaza24 = new PlazaZona($this->getUser(),"Equipo 24");
            $plaza24->setCompetencia($competenciaClasificacion);
            $zonaH->addPlazas($plaza24);
            /* Partido 8 - ZONA D */
            $partido8 = $competenciaClasificacion->newPartido($this->getUser(),"8");
            $partido8->setPlaza1($plaza22);
            $partido8->setPlaza2($plaza23);
            $zonaH->addPartido($partido8);
            /* Partido 16 - ZONA D */
            $partido16 = $competenciaClasificacion->newPartido($this->getUser(),"16");
            $partido16->setPlaza1($plaza22);
            $partido16->setPlaza2($plaza24);
            $zonaH->addPartido($partido16);
            /* Partido 24 - ZONA D */
            $partido24 = $competenciaClasificacion->newPartido($this->getUser(),"24");
            $partido24->setPlaza1($plaza23);
            $partido24->setPlaza2($plaza24);
            $zonaH->addPartido($partido24);
            $competenciaClasificacion->addZona($zonaH);
                        
            $etapaClasificacion->setCompetencia($competenciaClasificacion);
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaEliminacionDirecta".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $numPartido = 25;
            
            /* 8vos de Final*/
            $partido8vos1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos1->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona A",99,$competenciaFinal));
            $partido8vos1->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona E",99,$competenciaFinal));
            $partido8vos1->setNivel(4);
            $partido8vos1->setOrden(1);
            $competenciaFinal->addPartido($partido8vos1);
            $numPartido++;

            $partido8vos2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos2->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona A",99,$competenciaFinal));
            $partido8vos2->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona e",99,$competenciaFinal));
            $partido8vos2->setNivel(4);
            $partido8vos2->setOrden(2);
            $competenciaFinal->addPartido($partido8vos2);
            $numPartido++;            
            
            $partido8vos3 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos3->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona B",99,$competenciaFinal));
            $partido8vos3->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona F",99,$competenciaFinal));
            $partido8vos3->setNivel(4);
            $partido8vos3->setOrden(3);
            $competenciaFinal->addPartido($partido8vos3);
            $numPartido++;
            
            $partido8vos4 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos4->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona B",99,$competenciaFinal));
            $partido8vos4->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona F",99,$competenciaFinal));
            $partido8vos4->setNivel(4);
            $partido8vos4->setOrden(4);
            $competenciaFinal->addPartido($partido8vos4);
            $numPartido++;
            
            $partido8vos5 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos5->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona C",99,$competenciaFinal));
            $partido8vos5->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona G",99,$competenciaFinal));
            $partido8vos5->setNivel(4);
            $partido8vos5->setOrden(5);
            $competenciaFinal->addPartido($partido8vos5);
            $numPartido++;

            $partido8vos6 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos6->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona C",99,$competenciaFinal));
            $partido8vos6->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona G",99,$competenciaFinal));
            $partido8vos6->setNivel(4);
            $partido8vos6->setOrden(6);
            $competenciaFinal->addPartido($partido8vos6);
            $numPartido++;            
            
            $partido8vos7 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos7->setPlaza1(new PlazaCopa($this->getUser(),"Primero Zona D",99,$competenciaFinal));
            $partido8vos7->setPlaza2(new PlazaCopa($this->getUser(),"Segundo Zona H",99,$competenciaFinal));
            $partido8vos7->setNivel(4);
            $partido8vos7->setOrden(7);
            $competenciaFinal->addPartido($partido8vos7);
            $numPartido++;
            
            $partido8vos8 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido8vos8->setPlaza1(new PlazaCopa($this->getUser(),"Segundo Zona D",99,$competenciaFinal));
            $partido8vos8->setPlaza2(new PlazaCopa($this->getUser(),"Primero Zona H",99,$competenciaFinal));
            $partido8vos8->setNivel(4);
            $partido8vos8->setOrden(8);
            $competenciaFinal->addPartido($partido8vos8);
            $numPartido++;
            
            /* 4tos de Final*/
            $partido4tos1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos1->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 25",99,$competenciaFinal));
            $partido4tos1->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 29",99,$competenciaFinal));
            $partido4tos1->setNivel(3);
            $partido4tos1->setOrden(1);
            $competenciaFinal->addPartido($partido4tos1);
            $numPartido++;

            $partido4tos2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos2->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 26",99,$competenciaFinal));
            $partido4tos2->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 30",99,$competenciaFinal));
            $partido4tos2->setNivel(3);
            $partido4tos2->setOrden(2);
            $competenciaFinal->addPartido($partido4tos2);
            $numPartido++;            
            
            $partido4tos3 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos3->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 27",99,$competenciaFinal));
            $partido4tos3->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 31",99,$competenciaFinal));
            $partido4tos3->setNivel(3);
            $partido4tos3->setOrden(3);
            $competenciaFinal->addPartido($partido4tos3);
            $numPartido++;
            
            $partido4tos4 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partido4tos4->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 28",99,$competenciaFinal));
            $partido4tos4->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 32",99,$competenciaFinal));
            $partido4tos4->setNivel(3);
            $partido4tos4->setOrden(4);
            $competenciaFinal->addPartido($partido4tos4);
            $numPartido++;
            
            /* SEMIFINAL*/
            $partidoSemi1 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi1->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 33",99,$competenciaFinal));
            $partidoSemi1->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 35",99,$competenciaFinal));
            $partidoSemi1->setNivel(2);
            $partidoSemi1->setOrden(1);
            $competenciaFinal->addPartido($partidoSemi1);
            $numPartido++;

            $partidoSemi2 = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoSemi2->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 34",99,$competenciaFinal));
            $partidoSemi2->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 36",99,$competenciaFinal));            
            $partidoSemi2->setNivel(2);
            $partidoSemi2->setOrden(2);
            $competenciaFinal->addPartido($partidoSemi2);
            $numPartido++;
            
            if ($form->getData()['tercero']){
                $partido3ro = $competenciaFinal->newPartido($this->getUser(),$numPartido);
                $partido3ro->setPlaza1(new PlazaCopa($this->getUser(),"Perdedor Partido 37",99,$competenciaFinal));
                $partido3ro->setPlaza2(new PlazaCopa($this->getUser(),"Perdedor Partido 38",99,$competenciaFinal));                
                $partido3ro->setNivel(0);
                $partido3ro->setOrden(1);
                $competenciaFinal->addPartido($partido3ro);
                $numPartido++;
            }else{
                $medallero->getCompetencia()->addPlazas(new Plaza($this->getUser(),"Medalla de Bronce",3));
            }
            
            $partidoFinal = $competenciaFinal->newPartido($this->getUser(),$numPartido);
            $partidoFinal->setPlaza1(new PlazaCopa($this->getUser(),"Ganador Partido 37",99,$competenciaFinal));
            $partidoFinal->setPlaza2(new PlazaCopa($this->getUser(),"Ganador Partido 38",99,$competenciaFinal));                            
            $partidoFinal->setNivel(1);
            $partidoFinal->setOrden(1);
            $competenciaFinal->addPartido($partidoFinal);
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaClasificacion);
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaLigaTemplate8x3ActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaLigaTemplate8x3', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->add('tercero', 'checkbox', array(
                                    'label'    => 'Juega 3er puesto ?',
                                    "data" => true,
                                    'required' => false,
                    ))                    
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
    protected function createCompetenciaSerieFinalVaciaTemplateActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaSerieFinalVaciaTemplate', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a new CompetenciaSerie entities by etapa.
     *
     * @Route("/{id}/serie/final/vacia", name="resultado_CompetenciaSerieFinalVaciaTemplate")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaSerieFinalVaciaTemplateAction(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaSerieFinalVaciaTemplateActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaSerie".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $serie = new Serie($this->getUser(),"Final");
            $competenciaFinal->addSerie($serie);
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    
    
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaSerieFinalx12TemplateActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaSerieFinalx12Template', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a new CompetenciaSerie entities by etapa.
     *
     * @Route("/{id}/serie/final/x12", name="resultado_CompetenciaSerieFinalx12Template")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaSerieFinalx12TemplateAction(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaSerieFinalx12TemplateActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaSerie".$disciplinaParam->tipo;
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            
            $serie = new Serie($this->getUser(),"Final");
            $competenciaFinal->addSerie($serie);
            

            for ($i = 1; $i <= 12; $i++) {
                $entity = new PlazaSerie($this->getUser(),$serie->getCompetencia());
                $entity->setSerie($serie);
                $entity->setCompetencia($serie->getCompetencia());
                $entity->setNombre("Competidor ".$i);
                $serie->addPlazas($entity);
            }            
            
            $etapaFinal->setCompetencia($competenciaFinal);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaFinal);
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    
    
    /**
     * Creates a form to create a CompetenciaSerie entity.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCompetenciaSerie2x6TemplateActionForm(Evento $evento)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('resultado_CompetenciaSerie2x6Template', array('id' => $evento->getId())))
                    ->setMethod('POST')
                    ->getForm();
        ;
    }
    
    /**
     * Creates a new CompetenciaSerie entities by etapa.
     *
     * @Route("/{id}/serie/final/2x6", name="resultado_CompetenciaSerie2x6Template")
     * @Method("POST")
     * @Security("has_role('ROLE_COMPETENCIA_NEW')")
     * @Template()
     */
    public function createCompetenciaSerie2x6TemplateAction(Request $request, Evento $evento)
    {
        $form =  $this->createCompetenciaSerie2x6TemplateActionForm($evento);
        $form->handleRequest($request);
        
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        if(!$this->getUser()->hasAccessAtEvento($evento)){
            $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE El EVENTO NO TENGA ETAPAS CREADAS */
        if(count($evento->getEtapas())){
            $this->addFlash('primary', 'Este evento ya tiene definida una forma de juego. Antes de crear nuevas, elimine las actuales');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $disciplinaParam = json_decode($evento->getDisciplina()->getParametros());
        if(!is_object($disciplinaParam) || !property_exists($disciplinaParam,"tipo")){
            $this->addFlash('primary', 'La disciplina no tiene el parámetro TIPO definido. Contacte al administrador.');
            return $this->redirect($this->getRequest()->headers->get('referer'));            
        }
        
        if ($form->isValid()) {
            //Etapa de clasificación
            $etapaClasificacion = new EtapaClasificacion($this->getUser());
            $etapaClasificacion->setNombre("Etapa de Clasificación");
            $etapaClasificacion->setEvento($evento);            
            //Etapa final
            $etapaFinal = new EtapaFinal($this->getUser());
            $etapaFinal->setNombre("Etapa Final");
            $etapaFinal->setEvento($evento);
            //medallero
            $medallero = new EtapaMedallero($this->getUser());
            $medallero->setNombre("Medallero");
            $medallero->setEvento($evento);
            
            /* ETAPA FINAL */
            $tipo = "ResultadoBundle\Entity\CompetenciaSerie".$disciplinaParam->tipo;
            
            $competenciaClasificacion = new $tipo($this->getUser());
            $competenciaClasificacion->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaClasificacion->setEtapa($etapaClasificacion);
            
            $competenciaFinal = new $tipo($this->getUser());
            $competenciaFinal->setNombre("Competencia para ".$etapaFinal->getNombre());
            $competenciaFinal->setEtapa($etapaFinal);
            for ($j = 1; $j <= 2; $j++) {
                $serie = new Serie($this->getUser(),"Serie ".$j);
                $competenciaClasificacion->addSerie($serie);
                for ($i = 1; $i <= 6; $i++) {
                    $entity = new PlazaSerie($this->getUser(),$serie->getCompetencia());
                    $entity->setSerie($serie);
                    $entity->setCompetencia($serie->getCompetencia());
                    $entity->setNombre("Competidor ".$i);
                    $serie->addPlazas($entity);
                }
            }
            
            $serie = new Serie($this->getUser(),"Final");
            $competenciaFinal->addSerie($serie);
            

            for ($i = 1; $i <= 8; $i++) {
                $entity = new PlazaSerie($this->getUser(),$serie->getCompetencia());
                $entity->setSerie($serie);
                $entity->setCompetencia($serie->getCompetencia());
                $entity->setNombre("Competidor ".$i);
                $serie->addPlazas($entity);
            }
            
            $etapaFinal->setCompetencia($competenciaFinal);
            $etapaClasificacion->setCompetencia($competenciaClasificacion);
            
            $em = $this->getDoctrine()->getManager();
            //
            $em->persist($etapaClasificacion);
            $em->persist($etapaFinal);
            
            $em->persist($medallero);
            
            try{
                $em->flush();
                $this->addFlash('exito', 'La forma de juego fue asignada correctamente.');
                return $this->redirect($this->getRequest()->headers->get('referer'));
            } catch (Exception $e) {
                $this->addFlash('error', 'La operación no puedo completarse.');
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }            
}
