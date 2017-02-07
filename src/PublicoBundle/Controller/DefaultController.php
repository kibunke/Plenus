<?php

namespace PublicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use CommonBundle\Entity\Municipio;

/**
 * Default controller.
 *
 * @Route("/publico")
 */
class DefaultController extends Controller
{
    /**
     * index publico
     * 
     * @Route("/", name="publico_index")
     * @Method({"GET","POST"})
     * @Template("PublicoBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request) {
        //return $this->redirect($this->generateUrl('publico_medallero'));
        $em = $this->getDoctrine()->getManager();
        $eventos = [];
        $municipio = null;
        $cronogramas = [];
        $formMunicipio = $this->createSearchMunicipioForm($request);
        $formEvento = $this->createSearchEventoForm($request);
        $formNroEvento = $this->createSearchNroEventoForm();
        $medalleroGral = $this->getMedalleroGeneral();
       
        return array(
            'cronogramas' => $cronogramas,
            'municipio' => $municipio,
            'eventos' => $eventos,
            'formMunicipio'   => $formMunicipio->createView(),
            'formEvento'   => $formEvento->createView(),
            'formNroEvento'   => $formNroEvento->createView(),
            'medalleroGral' => $medalleroGral
        );
    }
    
    /**
     * index publico
     * 
     * @Route("/escenarios", name="publico_index_escenarios")
     * @Method({"GET", "POST"})
     * @Template("PublicoBundle:Default:escenarios.html.twig")
     */
    public function escenarioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $escenarios = $em->getRepository('ResultadoBundle:Escenario')->findAll();
        return array(
            'escenarios' => $escenarios
        );
    }
    
    /**
     * buscar por municipio + fecha publico
     * 
     * @Route("/buscar/municipio", name="publico_search_municipio")
     * @Method({"GET","POST"})
     * @Template("PublicoBundle:Default:index.html.twig")
     */
    public function buscarPorMunicipioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $request->getSession()->set('ultEvento','');
        $municipio = null;
        $cronogramas = [];
        $formEvento = $this->createSearchEventoForm();
        $formMunicipio = $this->createSearchMunicipioForm($request);
        $formNroEvento = $this->createSearchNroEventoForm();
        $formMunicipio->handleRequest($request);
        $medalleroGral = $this->getMedalleroGeneral();
        
        if ($formMunicipio->isValid()) {
            $request->getSession()->set('ultFecha', $formMunicipio->getData()['Fecha']);
            if ($formMunicipio->getData()['Municipio']){
                $request->getSession()->set('ultMunicipio', $formMunicipio->getData()['Municipio']->getId());
                $municipio = $em->getRepository('CommonBundle:Partido')->find($formMunicipio->getData()['Municipio']);
            }else{
                $municipio = null;
            }
            $cronogramas = $em->getRepository('ResultadoBundle:Cronograma')->getAllByMunicipio($municipio,$formMunicipio->getData()['Fecha']);
            $cronogramas_p = $em->getRepository('ResultadoBundle:Cronograma')->getAllPartidosByMunicipio($municipio,$formMunicipio->getData()['Fecha']);
            $cronogramas = array_merge($cronogramas,$cronogramas_p);
        }
       
        return array(
            'cronogramas' => $cronogramas,
            'municipio' => $municipio,
            'evento' => null,
            'formMunicipio'   => $formMunicipio->createView(),
            'formEvento'   => $formEvento->createView(),
            'formNroEvento'   => $formNroEvento->createView(),
            'medalleroGral' => $medalleroGral
        );
    }
    
    /**
     * buscar por municipio + fecha publico
     * 
     * @Route("/buscar/evento", name="publico_search_evento")
     * @Method({"GET","POST"})
     * @Template("PublicoBundle:Default:index.html.twig")
     */
    public function buscarPorEventoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $request->getSession()->set('ultMunicipio', '');
        $municipio = null;
        $cronogramas = [];
        $formEvento = $this->createSearchEventoForm($request);
        $formMunicipio = $this->createSearchMunicipioForm();
        $formNroEvento = $this->createSearchNroEventoForm();
        $formEvento->handleRequest($request);
        $medalleroGral = $this->getMedalleroGeneral();
        
        if ($formEvento->isValid()) {
            $request->getSession()->set('ultFecha', $formEvento->getData()['Fecha']);
            //var_dump($formEvento->getData()['Evento']);die();
            if ($formEvento->getData()['Evento']){
                $request->getSession()->set('ultEvento', $formEvento->getData()['Evento']->getId());
                $evento = $em->getRepository('ResultadoBundle:Evento')->find($formEvento->getData()['Evento']);
            }else{
                $evento = null;
            }            
            
            //$evento = $em->getRepository('ResultadoBundle:Evento')->find($formEvento->getData()['Evento']);
            $cronogramas = $em->getRepository('ResultadoBundle:Cronograma')->getAllByEvento($evento,$formEvento->getData()['Fecha']);
            $cronogramas_p =  $em->getRepository('ResultadoBundle:Cronograma')->getAllPartidosByEvento($evento,$formEvento->getData()['Fecha']);
            $cronogramas = array_merge($cronogramas,$cronogramas_p);
            //$eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorMunicipio($municipio);
            //echo count($cronogramas_p);die;
        }
       
        return array(
            'cronogramas' => $cronogramas,
            'municipio' => $municipio,
            'evento' => $evento,
            'formMunicipio'   => $formMunicipio->createView(),
            'formEvento'   => $formEvento->createView(),
            'formNroEvento'   => $formNroEvento->createView(),
            'medalleroGral' => $medalleroGral
        );
    } 
    
    /**
     * buscar por municipio + fecha publico
     * 
     * @Route("/buscar/nroevento", name="publico_search_nroevento")
     * @Method({"GET","POST"})
     * @Template("PublicoBundle:Default:detalle.html.twig")
     */
    public function buscarPorNroEventoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $request->getSession()->set('ultMunicipio', '');
        $request->getSession()->set('ultEvento', '');
        $request->getSession()->set('ultFecha', '');
        $municipio = null;
        $formEvento = $this->createSearchEventoForm();
        $formMunicipio = $this->createSearchMunicipioForm();
        $formNroEvento = $this->createSearchNroEventoForm();
        $formNroEvento->handleRequest($request);
        $medalleroGral = $this->getMedalleroGeneral();
        
        if ($formNroEvento->isValid()) {
            $evento = $em->getRepository('ResultadoBundle:Evento')->find($formNroEvento->getData()['Evento']);
        }
       
        return array(
            'evento' => $evento,
            'formMunicipio'   => $formMunicipio->createView(),
            'formEvento'   => $formEvento->createView(),
            'formNroEvento'   => $formNroEvento->createView(),
            'medalleroGral' => $medalleroGral
        );
    }
    
    /**
    * Creates a form to edit a Equipo entity.
    *
    * @param Equipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createSearchMunicipioForm($request=null)
    {
        $em = $this->getDoctrine()->getManager();
        $mun = null;
        $fec = null;
        if (!is_null($request)){
            if ($request->getSession()->get('ultMunicipio'))
                $mun = $em->getReference("CommonBundle:Partido", $request->getSession()->get('ultMunicipio'));
            $fec = $request->getSession()->get('ultFecha');
        }
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('publico_search_municipio'))
                    ->setMethod('POST')
                    ->add('Municipio', 'entity', array(
                                                'class' => 'CommonBundle:Partido',
                                                'property' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('p')
                                                                                        ->where('p.provincia = 1')
                                                                                        ->orderBy('p.nombre');
                                                                    },
                                                'multiple' => false,
                                                'required' => false,
                                                'placeholder' => 'Seleccione',
                                                'data' => $mun
                                            )
                    )
                    ->add('Fecha', 'choice', array(
                                                'choices' => array(
                                                                    '0' => 'Todos',
                                                                    //'2015-09-22' => '22/09',
                                                                    '2016-10-03' => '03/10',
                                                                    '2016-10-04' => '04/10',
                                                                    '2016-10-05' => '05/10',
                                                                    '2016-10-06' => '06/10',
                                                                    '2016-10-07' => '07/10'
                                                                ),
                                                'data' => $fec
                                            )
                          )
                    ->add('submit', 'submit', array('label' => 'Buscar'))
                    ->getForm();
        ;
    }
    
    /**
    * Creates a form to edit a Equipo entity.
    *
    * @param Equipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createSearchEventoForm($request=null)
    {
        $em = $this->getDoctrine()->getManager();
        $fec = null;
        $ev = null;
        if (!is_null($request)){
            $ev = null;
            if ($request->getSession()->get('ultEvento'))
                $ev = $em->getReference("ResultadoBundle:Evento", $request->getSession()->get('ultEvento'));
            $fec = $request->getSession()->get('ultFecha');
        }

        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('publico_search_evento'))
                    ->setMethod('POST')
                    ->add('Evento', 'entity', array(
                                                'class' => 'ResultadoBundle:Evento',
                                                'property' => 'nombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('e')
                                                                                        ->join('e.disciplina','d')
                                                                                        ->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                //'placeholder' => 'Seleccione',
                                                //'group_by' => 'disciplina.nombreCompleto',
                                                'data' => $ev
                                            )
                    )
                    ->add('Fecha', 'choice', array(
                                                'choices' => array(
                                                                    '0' => 'Todos',
                                                                    //'2015-09-22' => '22/09',
                                                                    '2016-10-03' => '03/10',
                                                                    '2016-10-04' => '04/10',
                                                                    '2016-10-05' => '05/10',
                                                                    '2016-10-06' => '06/10',
                                                                    '2016-10-07' => '07/10'
                                                                ),
                                                'data' => $fec
                                            )
                          )
                    ->add('submit', 'submit', array('label' => 'Buscar'))
                    ->getForm();
        ;
    }
    
    /**
    * Creates a form to edit a Equipo entity.
    *
    * @param Equipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createSearchNroEventoForm($request=null)
    {
        return $this->createFormBuilder(array())
                    ->setAction($this->generateUrl('publico_search_nroevento'))
                    ->setMethod('POST')
                    ->add('Evento', 'text', array())
                    ->add('EventoAux', 'text', array('required' => false))
                    ->add('submit', 'submit', array('label' => 'Buscar'))
                    ->getForm();
        ;
    }    
    
    /**
     * detalle evento publico
     * 
     * @Route("/{evento}", name="publico_detalle_evento")
     * @Method({"GET"})
     * @Template()
     */
    public function detalleAction(Request $request,Evento $evento) {
        $formMunicipio = $this->createSearchMunicipioForm();
        $formEvento = $this->createSearchEventoForm();
        $formNroEvento = $this->createSearchNroEventoForm();
        return array(
            'evento' => $evento,
            'formMunicipio'   => $formMunicipio->createView(),
            'formEvento'   => $formEvento->createView(),
            'formNroEvento'   => $formNroEvento->createView()
        );
    }
    
    private function getMedalleroGeneral()
    {
        $em = $this->getDoctrine()->getManager();

        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        $listos = [];
        $municipios = [];
        $oro = []; $plata = []; $bronce = [];
        foreach($plazasMedallero as $plazaMedallero){
            if ($plazaMedallero->getEquipo()){
                $mun = $plazaMedallero->getEquipo()->getMunicipio();
                if (!in_array($mun->getId(), $listos)) {
                    $listos[]=$mun->getId();
                    $municipios[$mun->getId()]["region"] = "<small>".str_replace($mun->getRegionDeportiva(), "</small><strong>".$mun->getRegionDeportiva()."</strong><small>",  $mun->getCruceRegional())."</small>";
                    $municipios[$mun->getId()]["nombre"] = $mun->getNombre();
                    $municipios[$mun->getId()]["id"] = $mun->getId();
                    $municipios[$mun->getId()]["medallero"] = [0,0,0];
                    $oro[$mun->getId()] = 0;
                    $plata[$mun->getId()] = 0;
                    $bronce[$mun->getId()] = 0;
                }
                switch ($plazaMedallero->getOrden()){
                    case 1 : $oro[$mun->getId()] = $oro[$mun->getId()] + 1; break;
                    case 2 : $plata[$mun->getId()] = $plata[$mun->getId()] + 1; break;
                    case 3 : $bronce[$mun->getId()] = $bronce[$mun->getId()] + 1; break;
                }
                $municipios[$mun->getId()]["medallero"] = [$oro[$mun->getId()],$plata[$mun->getId()],$bronce[$mun->getId()]];
            }
        }

        $arr = array_multisort(
                    $oro, SORT_DESC, SORT_NUMERIC,
                    $plata, SORT_DESC, SORT_NUMERIC,
                    $bronce, SORT_DESC, SORT_NUMERIC,
                    $municipios
                );
        return $municipios;
    }
    /**
     * @Route("/medallero/general", name="publico_medallero")
     * @Method("GET")
     * @Template("PublicoBundle:Default:medallero.html.twig")
     */
    public function medalleroAction()
    {
        $municipios = $this->getMedalleroGeneral();

        return array(
            'municipios' => $municipios,
        );
    }

    /**
     * View Detalle medallero.
     *
     * @Route("/medallero/{id}/detalle", name="publicoMedallero_detalle")
     * @Method("GET")
     * @Template("PublicoBundle:Default:medalleroDetalle.html.twig")
     */
    public function medalleroDetalleAction(Request $request, Partido $municipio)
    {
        $em = $this->getDoctrine()->getManager();
        $plazas = $em->getRepository('ResultadoBundle:PlazaMedallero')->getAllByMunicipio($municipio);        

        $plazasMedallero = $em->getRepository('ResultadoBundle:PlazaMedallero')->findAll();
        $listos = [];
        $municipios = [];
        $oro = []; $plata = []; $bronce = [];
        foreach($plazasMedallero as $plazaMedallero){
            if ($plazaMedallero->getEquipo()){
                $mun = $plazaMedallero->getEquipo()->getMunicipio();
                if (!in_array($mun->getId(), $listos)) {
                    $listos[]=$mun->getId();
                    $municipios[$mun->getId()]["region"] = "<small>".str_replace($mun->getRegionDeportiva(), "</small><strong>".$mun->getRegionDeportiva()."</strong><small>",  $mun->getCruceRegional())."</small>";
                    $municipios[$mun->getId()]["nombre"] = $mun->getNombre();
                    $municipios[$mun->getId()]["id"] = $mun->getId();
                    $municipios[$mun->getId()]["medallero"] = [0,0,0];
                    $oro[$mun->getId()] = 0;
                    $plata[$mun->getId()] = 0;
                    $bronce[$mun->getId()] = 0;
                }
                switch ($plazaMedallero->getOrden()){
                    case 1 : $oro[$mun->getId()] = $oro[$mun->getId()] + 1; break;
                    case 2 : $plata[$mun->getId()] = $plata[$mun->getId()] + 1; break;
                    case 3 : $bronce[$mun->getId()] = $bronce[$mun->getId()] + 1; break;
                }
                $municipios[$mun->getId()]["medallero"] = [$oro[$mun->getId()],$plata[$mun->getId()],$bronce[$mun->getId()]];
            }
        }

        $arr = array_multisort(
                    $oro, SORT_DESC, SORT_NUMERIC,
                    $plata, SORT_DESC, SORT_NUMERIC,
                    $bronce, SORT_DESC, SORT_NUMERIC,
                    $municipios
                );
        $pos = 0;
        $it = 0;
        foreach($municipios as $item){
            if ($item['id'] == $municipio->getId())
                $pos = $it;
            $it++;
        }
        
        return array('plazas' => $plazas, 'pos' => $pos, 'municipios' => $municipios);
    }        
}
