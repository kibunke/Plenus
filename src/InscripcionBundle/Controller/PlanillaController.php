<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
use CommonBundle\PDFs\DocumentoPDF;
/**
 * Planilla controller.
 *
 * @Route("/planilla")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class PlanillaController extends Controller
{
    /**
     * @Route("/dashboard", name="planilla_dashboard")
     * @Method({"GET"})
     * @Security("has_role('ROLE_INSCRIPCION_DASHBOARD')")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $torneos = $em->getRepository('ResultadoBundle:Torneo')->findAll();
        $result = [];
        foreach ($torneos as $torneo){
            $result[$torneo->getId()] = $torneo->getJson();
        }
        $datos = $em->getRepository('InscripcionBundle:Planilla')->getDashboard();
        foreach ($datos as $dato){
            $result[$dato['id']]['datos']['inscriptos'][$dato['sexoNombre']] += $dato['sexo'];
            $result[$dato['id']]['datos']['inscriptos']['total'] += $dato['sexo'];
            $result[$dato['id']]['datos']['planillas'] += $dato['planillas'];
            $result[$dato['id']]['datos']['equipos'] += $dato['equipos'];
        }
        //var_dump($result);die;
        return array(
            'datos' => $result
        );
    }

    /**
     * Lists all Planilla entities.
     *
     * @Route("/list/misPlanillas", name="planilla_mis_list")
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_MIS_PLANILLAS')")
     * @Template()
     */
    public function misPlanillasListAction()
    {
        return array();
    }

    /**
     * Lists all Planilla entities.
     *
     * @Route("/list/misPendientes", name="planilla_pendientes_list")
     * @Method("GET")
     * @Template()
     */
    public function misPendientesListAction()
    {
        return array();
    }
    /**
     * show a Planilla entity.
     *
     * @Route("/{id}/show", name="planilla_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function showAction(Planilla $planilla)
    {
        return $this->render($planilla->getTemplateShow(), array(
            'planilla' => $planilla,
            'json' => json_encode($planilla->getJson())
        ));
    }

    /**
     * load Participante entity.
     *
     * @Route("/{dni}/load/participante", name="planilla_load_participante", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function loadParticipanteAction($dni)
    {
        $em = $this->getDoctrine()->getManager();
        $participante = $em->getRepository('ResultadoBundle:Competidor')->findOneBy(array('dni' => $dni));
        if ($participante){
            return new JsonResponse(array('success' => true, 'error' => false, 'participante' => $participante->getJson()));
        }
        return new JsonResponse(array('success' => false, 'error' => false, 'participante' => 'No se encontro el participante!'));
    }

    /**
     * @Route("/list/misPlanillas/datatable", name="planilla_mis_list_datatable", condition="request.isXmlHttpRequest()")
     * @Security("has_role('ROLE_INSCRIPCION_MIS_PLANILLAS')")
     * @Method("POST")
     */
    public function misPlanillasListDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Planilla')->datatable($request->request,$this->getUser(),$this->get('security.authorization_checker'),false);

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $planilla){
            $data['data'][] = array(
                "id"        => "<strong>".$planilla->getNumero()."</strong><br><small>". $planilla->getMunicipio()->getNombre()."</small>",
                "segmento"  => $planilla->getSegmento()->getNombreCompletoRaw(),
                "inscriptos"   => $planilla->getTotalInscriptos(),
                "estado"  => array(
                        "nombre" => $planilla->getEstado()->getNombreRaw(),
                        "observacion" => $planilla->getEstado()->getObservacion() ? $planilla->getEstado()->getObservacion() : '',
                        "auditoria"  => array(
                            "createdBy" => $planilla->getEstado()->getCreatedBy()->getNombreCompleto(),
                            "createdAt" => $planilla->getEstado()->getCreatedAt()->format('d/m/y H:i')
                        )
                    ),
                "auditoria"  => array(
                        "createdBy" => $planilla->getCreatedBy()->getNombreCompleto(),
                        "createdAt" => $planilla->getCreatedAt()->format('d/m/y H:i'),
                        "updatedAt" => $planilla->getUpdatedAt()?$planilla->getUpdatedAt()->format('d/m/y H:i'):''
                    ),
                "actions"   => $this->renderView('InscripcionBundle:Planilla:actions.html.twig', array('entity' => $planilla)),
            );
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/list/misPendientes/datatable", name="planilla_pendientes_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function misPendientesListDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Planilla')->datatable($request->request,$this->getUser(),$this->get('security.authorization_checker'),true);

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $planilla){
            $data['data'][] = array(
                "id"        => "<strong>".$planilla->getNumero()."</strong><br><small>". $planilla->getMunicipio()->getNombre()."</small>",
                "segmento"  => $planilla->getSegmento()->getNombreCompletoRaw(),
                "inscriptos"   => $planilla->getTotalInscriptos(),
                "estado"  => array(
                        "nombre" => $planilla->getEstado()->getNombreRaw(),
                        "observacion" => $planilla->getEstado()->getObservacion() ? $planilla->getEstado()->getObservacion() : '',
                        "auditoria"  => array(
                            "createdBy" => $planilla->getEstado()->getCreatedBy()->getNombreCompleto(),
                            "createdAt" => $planilla->getEstado()->getCreatedAt()->format('d/m/y H:i')
                        )
                    ),
                "auditoria"  => array(
                        "createdBy" => $planilla->getCreatedBy()->getNombreCompleto(),
                        "createdAt" => $planilla->getCreatedAt()->format('d/m/y H:i'),
                        "updatedAt" => $planilla->getUpdatedAt()?$planilla->getUpdatedAt()->format('d/m/y H:i'):''
                    ),
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
        if ($segmento->getMaxIntegrantes() == 1){
            $planilla = new Individual();
        }else{
            $planilla = new Equipo();
        }
        $planilla->setSegmento($segmento);

        if (!$this->canEdit($planilla)){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La inscripción al segmento no está habilitada!'));
        }

        $form = $this->createForm(PlanillaType::class, $planilla);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $json = json_decode($form->get('data')->getData());
            //var_dump($json);//die;
            try {
                if (!$planilla->getMunicipio())
                    $planilla->setMunicipio($this->getUser()->getMunicipio());
                if ($this->loadPlanilla($planilla,$json)){
                    $planilla->setCreatedBy($this->getUser());
                    $estado = new Cargada();
                    $estado->setCreatedBy($this->getUser());
                    $planilla->addEstado($estado);
                    $em->persist($planilla);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'pathImp' => $this->generateUrl('planilla_print', array('segmento' => $segmento->getId(),'idPlanilla' => $planilla->getId())), 'message' => 'Planilla N°'. $planilla->getNumero()));
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
            $error = 'El email '.$aux.' ya está registrado';
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
        if ($json->responsableMunicipio && strlen($json->responsableMunicipio->nombre) > 2 && strlen($json->responsableMunicipio->apellido) > 2 && strlen($json->responsableMunicipio->dni) >= 6){
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
        if (strlen($json->nombre) > 2 && strlen($json->apellido) > 2 && strlen($json->dni) >= 6){
            $planilla->setDirectorTecnicoNombre($json->nombre);
            $planilla->setDirectorTecnicoApellido($json->apellido);
            $planilla->setDirectorTecnicoDni($json->dni);
        }elseif($planilla->isTecnicoRequired()){
            throw new \Exception('Plenus: El Director Técnico es obligatorio en este segmento.');
        }
    }

    private function loadInstitucion($planilla, $json)
    {
        $em = $this->getDoctrine()->getManager();
        try{
            //$institucion = $em->getRepository('InscripcionBundle:Institucion')->findOneBy(array('nombre' => $json->nombre));
            $institucion = $planilla->getInstitucion();
            if (!$institucion){
                $institucion = Institucion::getInstance($this->getUser(),$json);
                $institucion->setMunicipio($planilla->getMunicipio());
            }else{
                $institucion->load($json);
                //if (strlen($json->telefono)>4 && strlen($institucion->getTelefono())<4)
                //    $institucion->setTelefono($json->telefono);
            }
        }catch(\Exception $e){
            if(strpos($e->getMessage(), 'Plenus:') !== false){
                throw $e;
            }
            throw new \Exception('Plenus: La institucion no se pudo crear. Ocurrio un error al persistir.');
        }
        $planilla->setInstitucion($institucion);
        return $institucion;
    }

    private function loadEquipo($jsonEquipo,$planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($jsonEquipo->id > 0){
            $equipo = $em->getRepository('ResultadoBundle:Equipo')->find($jsonEquipo->id);
            $toRemove = $equipo->cleanCompetidores();
            foreach($toRemove as $aux){
                $em->remove($aux);
            }
        }else{
            $equipo = $planilla->getNewEquipo();
        }

        $this->loadDirectorTecnico($planilla, $jsonEquipo->tecnico);
        try{
            foreach($jsonEquipo->integrantes as $jsonIntegrante){
                if (strlen($jsonIntegrante->persona->dni) >= 6){
                    $jsonIntegrante = $this->loadEntities($jsonIntegrante);

                    if ($jsonIntegrante && strlen($jsonIntegrante->persona->nombre) > 2 && strlen($jsonIntegrante->persona->apellido) > 2){

                        //Ojo que no contempla competidores que no sean participantes !
                        $integrante = $em->getRepository('ResultadoBundle:Competidor')->findOneBy(array('tipoDocumento' => $jsonIntegrante->persona->tipoDocumento , 'dni' => $jsonIntegrante->persona->dni));
                        if (!$integrante){
                                $integrante = new Competidor($this->getUser());
                                $integrante->loadFromJson($jsonIntegrante);
                        }
                        /*
                         * chequea que siempre represente al mismo municipio
                         * chequea que el municipio del participante sea igual al de la planilla
                         * si la inscripcion no es institucional.
                         * chequea que no esta inscripto en el mismo segmento
                         * Si no cumple algo genera una Exception en la clase planilla
                         */
                        $planilla->validarInscripcion($integrante);
                        //$integrante->inscripcionValida($planilla);

                        if ($integrante){
                            if ($planilla->inDateRange($integrante)){
                                $equipo->addIntegrante($integrante,$jsonIntegrante);
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
            if(strpos($e->getMessage(), 'Plenus:') !== false){
                throw $e;
            }
            throw $e;
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
            $json->persona->fNacimiento = explode("/",$json->persona->fNacimiento);
            $json->persona->fNacimiento = new \DateTime($json->persona->fNacimiento[2].'-'.$json->persona->fNacimiento[1]."-".$json->persona->fNacimiento[0]);
            $json->persona->sexo = $em->getRepository('ResultadoBundle:Genero')->findOneBy(array('nombre' => $json->persona->sexo));
            if (is_object($json->persona->municipio) &&
                is_object($json->persona->tipoDocumento) &&
                is_object($json->persona->fNacimiento) &&
                is_object($json->persona->sexo)){
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
        if (!$planilla->isEditable($this->getUser())){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no puede ser editada por usted en este estado!'));
        }
        if (!$this->canEdit($planilla)){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La inscripción al segmento no está habilitada!'));
        }

        $form = $this->createForm(PlanillaType::class, $planilla);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $json = json_decode($form->get('data')->getData());
            //var_dump($json);//die;
            try {
                if ($this->loadPlanilla($planilla,$json)){
                    $planilla->setUpdatedBy($this->getUser());
                    $planilla->setUpdatedAt(new \DateTime());
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'pathImp' => $this->generateUrl('planilla_print', array('segmento' => $planilla->getSegmento()->getId(),'idPlanilla' => $planilla->getId())), 'message' => 'Planilla N° '. $planilla->getNumero()));
                }else{
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no tiene Participantes!. Debe completar los campos obligatorios en la tabla de participantes para continuar.'));
                }
            }catch(\Exception $e ){
                $error = $this->processException($e);
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => $error, 'debug' => $e->getMessage()));
            }
        }
        return $this->render($planilla->getTemplate(), array(
            'form' => $form->createView(),
            'planilla' => $planilla
        ));
    }

    /**
     * @Route("/{id}/delete", name="planilla_delete", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function deleteAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($planilla){
            if ($planilla->isRemovable($this->getUser())){
                try {
                    $toRemove = $planilla->prepareToDelete();
                    foreach($toRemove as $aux){
                        $em->remove($aux);
                    }
                    $em->remove($planilla);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se eliminó la planilla!'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no puede eliminarse en su estado actual.'));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no está registrada en el sistema.'));
    }

    private function canEdit($planilla)
    {
        if ($this->isGranted('ROLE_INSCRIPCION_FUERA_TERMINO')){
            return true;
        }elseif ($planilla->getSegmento()->getIsActive()){
            return true;
        }

        return false;
    }

    /**
     * Print a Planilla entity.
     *
     * @Route("/print/{segmento}/{idPlanilla}", name="planilla_print", defaults={"idPlanilla" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_INSCRIPCION_PLANILLA_PRINT')")
     */
    public function printAction(Request $request,Segmento $segmento, $idPlanilla)
    {
        $em = $this->getDoctrine()->getManager();
        //var_dump($idPlanilla);
        if ($idPlanilla){
            $planilla = $em->getRepository('InscripcionBundle:Planilla')->find($idPlanilla);
        }else{
            if ($segmento->getMaxIntegrantes() == 1){
                $planilla = new Individual();
            }else{
                $planilla = new Equipo();
            }
            $planilla->setSegmento($segmento);
        }

        $pdf = new DocumentoPDF();
        $pdf->setMargenTop(2);
        $pdf->setMargenRight(10);
        $pdf->setMargenLeft(5);
        $pdf->init();

        $pdf->deletePage(1);
        $pdf->AddPage('L', 'LEGAL');
        $html = '';
        //$html = '<table cellspacing="0" cellpadding="0">
        //            <tr>
        //                <th style="border-bottom: 1px solid silver;width: 50%" align="left">
        //                    <img src="'.$request->getBasePath().'/assets/images/logos/logojuegos.png'.'" style="height:50px">
        //                </th>
        //                <th style="border-bottom: 1px solid silver;width: 50%" align="right">
        //                    <img src="'.$request->getBasePath().'/assets/images/logos/buenosAiresProvBlack.png'.'" style="height:50px;">
        //                </th>
        //            </tr>
        //        </table>';
        $txtModalidad = $segmento->getModalidad()->getNombre();
        $txtModalidad .= $segmento->getNombre()? " - " . $segmento->getNombre() : '';
        $municipio = $planilla->getMunicipio() ? $planilla->getMunicipio()->getNombre():'Municipio:';
        $nPlanilla = "N° ".$planilla->getNumero();
        $html .= '<table cellspacing="0" cellpadding="3" style="width:100%;">
                    <tr>
                        <th style="border-bottom: 1px solid silver;width: 22%" align="left" rowspan="4">
                            <img src="'.$request->getBasePath().'/assets/images/logos/logojuegos.png'.'" style="height:250px">
                        </th>
                        <th style="width: 37%; text-align:center;font-size:16px;color:#999" rowspan="4">
                            <b>Solicitud de inscripción </b><br>
                            Lista de buena fe
                            <div style="font-size:17px;width:100%;'.($planilla->getMunicipio() ? "text-align:center" : "text-align:left").'"><b>'.$municipio.'</b></div>
                            <b style="">'.$nPlanilla.'</b>
                        </th>
                        <th style="width: 15%;border-bottom: 1px solid silver;text-align:right;font-size:12px;color:#999;font-weight:700">Disciplina</th>
                        <td style="width: 25%;border-bottom: 1px solid silver;text-align:left;font-size:12px;">'.$segmento->getDisciplina()->getNombreCompleto().'</td>
                    </tr>
                    <tr>
                        <th style="border-bottom: 1px solid silver;text-align:right;font-size:12px;color:#999;font-weight:700">Genero</th>
                        <td style="border-bottom: 1px solid silver;text-align:left;font-size:12px;">'.$segmento->getGenero()->getNombre().'</td>
                    </tr>
                    <tr>
                        <th style="border-bottom: 1px solid silver;text-align:right;font-size:12px;color:#999;font-weight:700">Categoria</th>
                        <td style="border-bottom: 1px solid silver;text-align:left;font-size:12px;">'.$segmento->getCategoria()->getNombre().'</td>
                    </tr>
                    <tr>
                        <th style="text-align:right;color:#999;font-weight:700;font-size:12px">Modalidad</th>
                        <td style="text-align:left;font-size:12px;">'.$txtModalidad.'</td>
                    </tr>
                </table>';
        $arrPlanilla = $planilla->getJson();
        $html .= $this->getTableEquiposHTML($arrPlanilla);
        $html .= $this->getTableFooter($arrPlanilla);
        $html .= '<div style="font-size:7px">
            * Por la mera circunstancia de suscribir la presente Lista de Buena Fe, el aspirante se obliga a respetar en todos sus términos y extensión el Reglamento General, que declara bajo juramento conocer y aceptar.<br>
            * Reconoce a titulo confesional como único organismo facultado para su aplicación al Tribunal de Disciplina allí establecido, o el órgano que en futuro pudiera reemplazarlo, consintiendo expresamente lo establecido por el Artículo 28 del Reglamento, en lo concerniente a la irrecurribilidad de sus decisiones.<br>
            * La presente planilla debe ser confeccionada a máquina o con letra tipo imprenta, consignándose la totalidad de los datos solicitados, que se consideran como DECLARACION JURADA.<br>
            * Acepto que los datos proporcionados sean utilizados por el Gobierno de la provincia de Buenos Aires para envíos de información Institucional.<br>
        </div>';
        $pdf->writeHTML($html, true, false, true, false, '');
        return new Response($pdf->Output($segmento->getNombreCompleto().'.pdf','D'));
    }

    private function getTableEquiposHTML($arrPlanilla)
    {
        $blankRow =    '<tr>
                            <th style="text-align:center;border:1px solid silver;background-color:#ddd">%nROW%</th>
                            <td style="text-align:center;border:1px solid silver"></td>
                            <td style="border:1px solid silver"></td>
                            <td style="border:1px solid silver"></td>
                            <td style="border:1px solid silver"></td>
                            <td style="text-align:center;border:1px solid silver"></td>
                            <td style="text-align:center;border:1px solid silver"></td>
                            <td style="border:1px solid silver"></td>
                            <td style="text-align:center;border:1px solid silver"></td>
                            <td style="border:1px solid silver"></td>
                        </tr>';
        $html = '
                <table cellspacing="0" cellpadding="2" style="width:100%;font-size:10px;">
                    <tr>
                        <th style="width:3%;text-align:center;border:1px solid silver">#</th>
                        <th style="width:8%;text-align:center;border:1px solid silver">N° documento</th>
                        <th style="width:15%;border:1px solid silver">Apellido</th>
                        <th style="width:15%;border:1px solid silver">Nombres</th>
                        <th style="width:4%;text-align:center;border:1px solid silver">Sexo</th>
                        <th style="width:8%;text-align:center;border:1px solid silver">F. nacimiento</th>
                        <th style="width:10%;text-align:center;border:1px solid silver">Teléfono</th>
                        <th style="width:16%;border:1px solid silver">Municipio</th>
                        <th style="width:16%;text-align:center;border:1px solid silver">E-mail</th>
                        <th style="width:5%;border:1px solid silver">Obs.</th>
                    </tr>';
        //$arrPlanilla = $planilla->getJson();
        $cantRows = 0;
        foreach( $arrPlanilla['equipos'] as $equipo ){
            foreach( $equipo['integrantes'] as $competidor ){
                if ($competidor['rol'] == 'inscripto' ){
                    $cantRows++;
                    $html .=    '<tr>
                                    <th style="text-align:center;border:1px solid silver;background-color:#ddd">'.$cantRows.'</th>
                                    <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['dni'].'</td>
                                    <td style="border:1px solid silver">'.$competidor['persona']['apellido'].'</td>
                                    <td style="border:1px solid silver">'.$competidor['persona']['nombre'].'</td>
                                    <td style="border:1px solid silver">'.substr($competidor['persona']['sexo'],0,1).'</td>
                                    <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['fNacimiento'].'</td>
                                    <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['telefono'].'</td>
                                    <td style="border:1px solid silver">'.$competidor['persona']['municipio'].'</td>
                                    <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['email'].'</td>
                                    <td style="border:1px solid silver"></td>
                                </tr>';
                }
            }
        }
        if ($arrPlanilla['parametros']['maxEqPlanilla'] > 1){
            for( $i = $cantRows; $i < $arrPlanilla['parametros']['maxEqPlanilla']; $i++){
                $cantRows++;
                $html .=  str_replace('%nROW%',$cantRows, $blankRow);

            }
        }else{
            for( $i = $cantRows; $i < $arrPlanilla['parametros']['maxIntegrantes']; $i++){
                $cantRows++;
                $html .=  str_replace('%nROW%',$cantRows, $blankRow);

            }
        }
        if ($arrPlanilla['parametros']['maxReemplazos'] > 0){
            $html .=    '<tr><th colspan="10" style="border:1px solid silver;background-color:#ddd;text-align:center">Sustitutos</th></tr>';
            $cantRowsSustituto = 0;
            foreach( $arrPlanilla['equipos'] as $equipo ){
                foreach( $equipo['integrantes'] as $competidor ){
                    if ($competidor['rol'] != 'inscripto' ){
                        $cantRowsSustituto++;
                        $html .=    '<tr>
                                        <th style="text-align:center;border:1px solid silver;background-color:#ddd">'.$cantRows.'</th>
                                        <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['dni'].'</td>
                                        <td style="border:1px solid silver">'.$competidor['persona']['apellido'].'</td>
                                        <td style="border:1px solid silver">'.$competidor['persona']['nombre'].'</td>
                                        <td style="border:1px solid silver">'.substr($competidor['persona']['sexo'],0,1).'</td>
                                        <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['fNacimiento'].'</td>
                                        <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['telefono'].'</td>
                                        <td style="border:1px solid silver">'.$competidor['persona']['municipio'].'</td>
                                        <td style="text-align:center;border:1px solid silver">'.$competidor['persona']['email'].'</td>
                                        <td style="border:1px solid silver"></td>
                                    </tr>';
                    }
                }
            }
            for( $i = $cantRowsSustituto; $i < $arrPlanilla['parametros']['maxReemplazos']; $i++){
                $cantRowsSustituto++;
                $html .=  str_replace('%nROW%',$cantRowsSustituto, $blankRow);
            }
        }
        return $html .='</table>';
    }

    private function getTableFooter($arrPlanilla)
    {
        $tecnico = sizeof($arrPlanilla['equipos']) ? $arrPlanilla['equipos'][0]['tecnico'] :null;
        $institucion = $arrPlanilla['institucion'] ? $arrPlanilla['institucion'] : null;
        $html = '
            <div></div>
            <table cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;">
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="1" style="width:100%;font-size:10px;">
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd" class="text-center" colspan="2">Director técnico</th>
                            </tr>
                            <tr>
                                <th style="width:30%;border:1px solid silver;background-color:#ddd">Apellido</th>
                                <td style="width:70%;border:1px solid silver;">'.($tecnico ? $tecnico['apellido'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Nombre</th>
                                <td style="border:1px solid silver;">'.($tecnico ? $tecnico['nombre'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Documento</th>
                                <td style="border:1px solid silver;">'.($tecnico ? $tecnico['dni'] :'').'</td>
                            </tr>
                            <tr class="sello">
                                <th style="border:1px solid silver;background-color:#ddd;height:30px">Firma</th>
                                <td style="border:1px solid silver;"></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellspacing="0" cellpadding="1" style="width:100%;font-size:10px;float:left">
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd" class="text-center" colspan="2">Institución a la que representa</th>
                            </tr>
                            <tr>
                                <th style="width:30%;border:1px solid silver;background-color:#ddd">Nombre</th>
                                <td style="width:70%;border:1px solid silver;">'.($institucion ? $institucion['nombre'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Domicilio</th>
                                <td style="border:1px solid silver;">'.($institucion ? $institucion['domicilio'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Telefono</th>
                                <td style="border:1px solid silver;">'.($institucion ? $institucion['telefono'] :'').'</td>
                            </tr>
                            <tr class="sello">
                                <th style="border:1px solid silver;background-color:#ddd;height:30px">Sello</th>
                                <td style="border:1px solid silver;"></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellspacing="0" cellpadding="1" style="width:100%;font-size:10px">
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd" class="text-center" colspan="2">Responsable Institución</th>
                            </tr>
                            <tr>
                                <th style="width:30%;border:1px solid silver;background-color:#ddd">Apellido</th>
                                <td style="width:70%;border:1px solid silver;">'.($institucion ? $institucion['responsable']['apellido'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Nombre</th>
                                <td style="border:1px solid silver;">'.($institucion ? $institucion['responsable']['nombre'] :'').'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Documento</th>
                                <td style="border:1px solid silver;">'.($institucion ? $institucion['responsable']['dni'] :'').'</td>
                            </tr>
                            <tr class="sello">
                                <th style="border:1px solid silver;background-color:#ddd;height:30px">Firma y Sello</th>
                                <td style="border:1px solid silver;"></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellspacing="0" cellpadding="1" style="width:100%;font-size:10px">
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd" class="text-center" colspan="2">Responsable Municipio</th>
                            </tr>
                            <tr>
                                <th style="width:30%;border:1px solid silver;background-color:#ddd">Apellido</th>
                                <td style="width:70%;border:1px solid silver;">'.$arrPlanilla['responsableMunicipio']['apellido'].'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Nombre</th>
                                <td style="border:1px solid silver;">'.$arrPlanilla['responsableMunicipio']['nombre'].'</td>
                            </tr>
                            <tr>
                                <th style="border:1px solid silver;background-color:#ddd">Documento</th>
                                <td style="border:1px solid silver;">'.$arrPlanilla['responsableMunicipio']['dni'].'</td>
                            </tr>
                            <tr class="sello">
                                <th style="border:1px solid silver;background-color:#ddd;height:30px">Firma y Sello</th>
                                <td style="border:1px solid silver;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>';

        return $html;
    }

}
