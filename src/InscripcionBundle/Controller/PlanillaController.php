<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\Planilla;
use InscripcionBundle\Entity\Cargada;
use InscripcionBundle\Entity\Institucion;
use InscripcionBundle\Entity\Individual;
use InscripcionBundle\Entity\Equipo;
use ResultadoBundle\Entity\DirectorTecnico;
use InscripcionBundle\Form\PlanillaType;
use InscripcionBundle\Entity\Segmento;
use ResultadoBundle\Entity\Competidor;

/**
 * Planilla controller.
 *
 * @Route("/planilla")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class PlanillaController extends Controller
{
    /**
     * Lists all Planilla entities.
     *
     * @Route("/list", name="planilla_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        return array();        
    }
    
    /**
     * show a Planilla entity.
     *
     * @Route("/{id}/show", name="planilla_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="planilla_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Planilla')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $planilla){
            $data['data'][] = array(
                "id"        => $planilla->getId(),
                "segmento"  => $planilla->getSegmento()->getNombreCompletoRaw(),
                "inscriptos"   => $planilla->getTotalInscriptos(),
                "estado"  => $planilla->getEstado()->getNombreRaw(),
                "actions"   => $this->renderView('InscripcionBundle:Planilla:actions.html.twig', array('entity' => $planilla)),
            );
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/new/{id}", name="planilla_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_INSCRIPCION_PLANILLA_NEW')")
     */
    public function newAction(Request $request, Segmento $segmento)
    {        
        $em = $this->getDoctrine()->getManager();
        if (!$this->canEdit($segmento)){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La inscripción al segmento no está habilitada!'));
        }
        
        if ($segmento->getMaxIntegrantes() == 1){
            $planilla = new Individual();
        }else{
            $planilla = new Equipo();
        }
        
        $planilla->setSegmento($segmento);
        $form = $this->createForm(PlanillaType::class, $planilla);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $json = json_decode($form->get('data')->getData());
            //var_dump($json);//die;
            try {
                if ($this->loadPlanilla($planilla,$json)){
                    $planilla->setCreatedBy($this->getUser());
                    $estado = new Cargada();
                    $estado->setCreatedBy($this->getUser());
                    $planilla->addEstado($estado);
                    $em->persist($planilla);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se creo la Planilla'));
                }else{
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no tiene Participantes!. Debe completar los campos obligatorios en la tabla de participantes para continuar.'));
                }
            }catch(\Exception $e ){
                $error = $this->processException($e);
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => $error, 'debug' => $e->getMessage()));
            }
        }
        if ($form->isSubmitted()){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Faltan datos en la planilla! '.(string) $form->getErrors(true, false)));
        }
        return $this->render($planilla->getTemplate(), array(
            'form' => $form->createView(),
            'planilla' => $planilla
        ));
    }
    private function processException($e)
    {
        $error = 'Ocurrio un error al intentar guardar los datos.';
        if(strpos($e->getMessage(), 'unique_email') !== false){
            $aux = explode("Duplicate entry '", $e->getMessage())[1];
            $aux = explode("'",$aux)[0];
            $error = 'El email '.$aux.' ya está registrado' . $e->getMessage();
        }elseif(strpos($e->getMessage(), 'unique_dni') !== false){
            $aux = explode("Duplicate entry '", $e->getMessage())[1];
            $aux = explode("'",$aux)[0];
            $error = 'El DNI '.$aux.' ya está registrado y no puede ser una participante!';
        }elseif(strpos($e->getMessage(), 'Plenus:') !== false){
            $error = $e->getMessage();
        }
        return $error;
    }
    
    private function loadPlanilla($planilla, $json)
    {
        $em = $this->getDoctrine()->getManager();
        if ($json->inscripcionInstitucional)
            $this->loadInstitucion($planilla,$json->institucion);
        if ($json->responsableMunicipio && strlen($json->responsableMunicipio->nombre)>2 && strlen($json->responsableMunicipio->apellido)>2){
            $planilla->setResponsableMunicipioNombre($json->responsableMunicipio->nombre);
            $planilla->setResponsableMunicipioApellido($json->responsableMunicipio->apellido);
            $planilla->setResponsableMunicipioDni($json->responsableMunicipio->dni);
            foreach($json->equipos as $jsonEquipo){
                $equipo = $this->loadEquipo($jsonEquipo,$planilla);
                if ($equipo){
                    
                    $planilla->addEquipo($equipo);
                }
            }
            if (count($planilla->getEquipos())){
                return true;
            }
        }else{
            throw new \Exception('Plenus: Datos del Responsable Municipio inválidos o incompletos.');
        }
        return false;
    }
    
    private function loadDirectorTecnico($planilla, $json)
    {
        $em = $this->getDoctrine()->getManager();
        try{
            $json->tipoDocumento = $em->getRepository('CommonBundle:TipoDocumento')->findOneBy(array('nombre' => $json->tipoDocumento));
            $tecnico = $em->getRepository('ResultadoBundle:DirectorTecnico')->findOneBy(array('tipoDocumento' => $json->tipoDocumento , 'dni' => $json->dni));

            if (!$tecnico){
                
                if (is_object($json->tipoDocumento) && strlen($json->nombre) > 2 && strlen($json->apellido) > 2 ){
                    $tecnico = new DirectorTecnico($this->getUser());
                    $tecnico->loadFromJson($json);
                    try {
                        $em->persist($tecnico);
                        $em->flush();
                    }catch(\Exception $e ){
                        throw new \Exception('Plenus: Los datos del Director Técnico no pudieron guardarse.'.$e->getMessage());
                    }
                }
                
                if ( !$tecnico && $planilla->isTecnicoRequired()){
                    throw new \Exception('Plenus: El Director Técnico es obligatorio en este segmento.');
                }
            }
        }catch(\Exception $e){
            if(strpos($e->getMessage(), 'Plenus:') !== false){
                throw $e;
            }
            throw new \Exception('Plenus: Datos del Director Tecnico inválidos o incompletos.');
        }
        return $tecnico;
    }
    
    private function loadInstitucion($planilla, $json)
    {
        $em = $this->getDoctrine()->getManager();
        try{
            $institucion = $em->getRepository('InscripcionBundle:Institucion')->findOneBy(array('nombre' => $json->nombre));
            if (!$institucion){
                $institucion = Institucion::getInstance($this->getUser(),$json);
            }    
        }catch(\Exception $e){
            //if(strpos($e->getMessage(), 'Plenus:') !== false){
                throw $e;
            //}
            throw new \Exception('Plenus: La institucion no se pudo crear. Ocurrio un error al persistir.');
        }
        $planilla->setInstitucion($institucion);
        return $institucion;
    }
    
    private function loadEquipo($jsonEquipo,$planilla)
    {
        $em = $this->getDoctrine()->getManager();
        $equipo = $planilla->getNewEquipo();
        if ($tecnico = $this->loadDirectorTecnico($planilla, $jsonEquipo->tecnico)){
            $equipo->setDirectorTecnico($tecnico);
        }
        try{
            foreach($jsonEquipo->integrantes as $jsonIntegrante){
                if (strlen($jsonIntegrante->persona->dni) > 6){
                    $jsonIntegrante = $this->loadEntities($jsonIntegrante);
                    
                    if ($jsonIntegrante && strlen($jsonIntegrante->persona->nombre) > 2 && strlen($jsonIntegrante->persona->apellido) > 2){
                        
                        //Ojo que no contempla competidores que no sean participantes !
                        $integrante = $em->getRepository('ResultadoBundle:Competidor')->findOneBy(array('tipoDocumento' => $jsonIntegrante->persona->tipoDocumento , 'dni' => $jsonIntegrante->persona->dni));
                        if (!$integrante){
                                $integrante = new Competidor($this->getUser());
                                $integrante->loadFromJson($jsonIntegrante);
                        }else{
                            /*
                             * chequea que siempre represente al mismo municipio
                             * chequea que no esta inscripto en el mismo segmento
                             * Si no cumple algo genera una Exception
                             */ 
                            $integrante->inscripcionValida($planilla);
                        }
                        if ($integrante){
                            if ($planilla->isOnDateRange($integrante)){
                                $equipo->addIntegrante($integrante);
                            }else{
                                if ($integrante->getId()){
                                    throw new \Exception('Plenus: El inscripto con DNI '.$jsonIntegrante->persona->dni.' ya existe en el sistema y su fecha de nacimiento cargada ('.$integrante->getFNacimiento()->format('d/m/Y').') se encuentra fuera del rango de fechas permitido por el segmento.');
                                }else{
                                    throw new \Exception('Plenus: El inscripto con DNI '.$jsonIntegrante->persona->dni.' está fuera del rango de fechas permitido.');
                                }
                            }
                        }
                    }else{
                        throw new \Exception('Plenus: El inscripto con DNI '.$jsonIntegrante->persona->dni.' no tiene todos los datos requeridos, o los ingresados son erroneos.');
                    }
                }else{
                    if (strlen($jsonIntegrante->persona->nombre) > 2 || strlen($jsonIntegrante->persona->apellido) > 2){
                        throw new \Exception('Plenus: Hay participantes que no tienen un DNI válido. Las filas no utilizadas deben quedar vacias.');
                    }
                }
            }
        }catch(\Exception $e){
            //if(strpos($e->getMessage(), 'Plenus:') !== false){
                throw $e;
            //}
            throw new \Exception('Plenus: Datos del Participante inválidos o incompletos.');
        }
        
        if (count($equipo->getIntegrantes())){
            return $equipo;
        }
        return false;
    }

    private function loadEntities($json)
    {
        $em = $this->getDoctrine()->getManager();
        if (isset($json->persona->dni) && isset($json->persona->municipio)){
            $json->persona->municipio = $em->getRepository('CommonBundle:Municipio')->findOneBy(array('nombre' => $json->persona->municipio));
            $json->persona->tipoDocumento = $em->getRepository('CommonBundle:TipoDocumento')->findOneBy(array('nombre' => $json->persona->tipoDocumento));
            $json->persona->fNacimiento = new \DateTime($json->persona->fNacimiento);
            if (is_object($json->persona->municipio) && is_object($json->persona->tipoDocumento) && is_object($json->persona->fNacimiento)){
                return $json;
            }
        }
        return null;
    }
    /**
     * @Route("/{id}/edit", name="planilla_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$this->canEdit($planilla)){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La inscripción al segmento no está habilitada!'));
        }
        
        $form = $this->createForm(PlanillaType::class, $planilla);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            try {
                //$segmento->setUpdatedAt(new \DateTime());
                //$segmento->setUpdatedBy($this->getUser());
                //foreach($segmento->getEventos() as $evento){
                //    $segmento->addEvento($evento);
                //}
                //$em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se modifico el Segmento'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return $this->render($planilla->getTemplate(), array(
            'form' => $form->createView(),
            'planilla' => $planilla
        ));
    }
    
    /**
     * @Route("/{id}/delete", name="segmento_delete", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function deleteAction(Request $request,Segmento $segmento)
    {
        $em = $this->getDoctrine()->getManager();
        if ($segmento){
            if(!count($segmento->getEventos())){
                try {
                    $em->remove($segmento);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se eliminó el Segmento'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El segmento no debe terner evenos asociados'));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El segmento no exite'));
    }
    
    private function canEdit($segmento)
    {
        if ($this->isGranted('ROLE_INSCRIPCION_FUERA_TERMINO')){
            return true;
        }elseif ($segmento->getIsActive()){
            return true;
        }
        
        return false;
    }
}