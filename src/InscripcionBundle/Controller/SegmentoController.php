<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\Segmento;
use InscripcionBundle\Form\SegmentoType;

/**
 * Segmento controller.
 *
 * @Route("/segmento")
 * @Security("has_role('ROLE_INSCRIPCION') and has_role('ROLE_SEGMENTO')")
 */
class SegmentoController extends Controller
{
    /**
     * Lists all Segmento entities.
     *
     * @Route("/tree", name="segmento_tree", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function treeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->parseSegmentos($em->getRepository('InscripcionBundle:Segmento')->getAllConNombre());
        return new JsonResponse($result['tree']);
    }

    private function addNodeDisciplina($aux,$segmento)
    {
        return [
                "text" => $aux[1],
                "children" => [$this->addNodeSegmento($aux,$segmento)]
            ];
    }
    private function addNodeSegmento($aux,$segmento)
    {
        return [
                    "text" => $segmento['segmento']."-".$aux[2]."-".$aux[3]."-".$aux[4],
                    "icon" => "fa fa-thumb-tack text-green fa-lg",
                    "id" => "ev-".$segmento['segmento']
            ];
    }

    private function parseSegmentos($segmentos)
    {
        $em = $this->getDoctrine()->getManager();
        $tree = [
                "text"=>"Todos mis segmentos",
                "state" => ["selected" => true],
                "children" => []
            ];
        $torneoIdAux=0;
        $disciplinaIdAux=0;
        $segmentosIds=[];
        foreach($segmentos as $segmento)
        {
            $aux = explode('-', $segmento['nombre']);
            $segmentosIds[] = $segmento['segmento'];
            $objDisciplina = $em->getRepository('ResultadoBundle:Disciplina')->find($segmento['disciplina']);
            $aux[1] = $objDisciplina->getNombreCompleto();
            if ($torneoIdAux == 0){
                $torneoAux = [
                                "text" => $aux[0],
                                "children" => []
                            ];
                $disciplinaAux = $this->addNodeDisciplina($aux,$segmento);
                $disciplinaIdAux = $segmento['disciplina'];
                $torneoIdAux = $segmento['torneo'];
            }else{
                if ($segmento['torneo'] == $torneoIdAux){
                    if ($segmento['disciplina'] == $disciplinaIdAux){
                        $disciplinaAux['children'][] = $this->addNodeSegmento($aux,$segmento);
                    }
                    else{
                        $torneoAux['children'][] = $disciplinaAux;
                        $disciplinaIdAux = $segmento['disciplina'];
                        $disciplinaAux = $this->addNodeDisciplina($aux,$segmento);
                    }
                }else{
                    $torneoAux['children'][] = $disciplinaAux;
                    $tree['children'][] = $torneoAux;
                    $torneoIdAux = $segmento['torneo'];
                    $disciplinaIdAux = $segmento['disciplina'];
                    $torneoAux = [
                                    "text" => $aux[0],
                                    "children" => []
                                ];
                    $disciplinaAux = $this->addNodeDisciplina($aux,$segmento);
                }
            }
        }
        $torneoAux['children'][] = $disciplinaAux;
        $tree['children'][] = $torneoAux;
        return array('tree'=>$tree,'ids'=>$segmentosIds);
    }

    /**
     * Lists all Segmento entities.
     *
     * @Route("/list", name="segmento_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        return array();
    }

    /**
     * show a Segmento entity.
     *
     * @Route("/{id}/show", name="segmento_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        return array();
    }

    /**
     * @Route("/list/datatable", name="segmento_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $user = $this->getUser();
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Segmento')->datatable($request->request,$user,$this->get('security.authorization_checker'));

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array(),
                    "state"           => array('actives' => $filter['actives'], 'inactives' => $filter['total']-$filter['actives'])
        );
        $logger = $this->get('logger');
        foreach ($filter['rows'] as $segmento){
            $inscriptos = $em->getRepository('InscripcionBundle:Segmento')->getTotalInscriptos($segmento,$user);
            $inscriptos = $segmento->getTotalInscriptosFromQuery($inscriptos);
            $data['data'][] = array(
                "id"        => $segmento->getId(),
                "segmento"  => $segmento->getNombreCompletoRaw(),
                "planillas"   => $em->getRepository('InscripcionBundle:Segmento')->getTotalPlanillas($user,$segmento),
                "eventos"   => $em->getRepository('InscripcionBundle:Segmento')->getTotalEventos($segmento),
                "coordinadores" => count($segmento->getCoordinadores()),
                "inscriptos"=> '<span class="text-danger" title="Planillas en cualquier estado / Planillas es estado Aprobadas">'.$inscriptos['total'].'</span> / <small class="text-success">'.$inscriptos['aprobadas'].'</small>',
                "parametros"=> array(
                    "max" => $segmento->getMaxIntegrantes(),
                    "min" => $segmento->getMinIntegrantes(),
                    "reemplazos" => $segmento->getMaxReemplazos(),
                    "maxFecha" => $segmento->getMaxFechaNacimiento()->format("d/m/Y"),
                    "minFecha" => $segmento->getMinFechaNacimiento()->format("d/m/Y"),
                ),
                "actions"   => $this->renderView('InscripcionBundle:Segmento:actions.html.twig', array('entity' => $segmento)),
            );
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="segmento_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SEGMENTO_NEW')")
     * @Template("InscripcionBundle:Segmento:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $segmento = new Segmento();
        $form = $this->createForm(SegmentoType::class, $segmento);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $segmento->setCreatedBy($this->getUser());
                $em->persist($segmento);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se creo el Segmento'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return array(
                'form' => $form->createView(),
            );
    }

    /**
     * @Route("/{id}/edit", name="segmento_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SEGMENTO_EDIT')")
     * @Template("InscripcionBundle:Segmento:edit.html.twig")
     */
    public function editAction(Request $request,Segmento $segmento)
    {
        if (!$this->canEdit($segmento)){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'No puede modificar este segmento!'));
        }
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SegmentoType::class, $segmento);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $segmento->setUpdatedAt(new \DateTime());
                $segmento->setUpdatedBy($this->getUser());
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se modifico el Segmento'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return array(
                'form' => $form->createView(),
            );
    }

    /**
     * @Route("/{id}/toggleState", name="segmento_state_toggle", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     * @Security("has_role('ROLE_SEGMENTO_ACTIVE')")
     */
    public function stateToggleAction(Request $request,Segmento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if ($entity){
            try {
                $entity->stateToggle();
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se cambió el estado'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El segmento no exite'));
    }

    /**
     * @Route("/{state}/toggleState/all", name="segmento_state_toggle_all", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function toggleStateAllAction(Request $request, $state)
    {
        $state = $state == '1';
        $em = $this->getDoctrine()->getManager();
        $segmentos = $em->getRepository('InscripcionBundle:Segmento')->findAll();
        foreach ($segmentos as $segmento){
            $segmento->setIsActive($state);
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'message' => 'Se cambio el estado a todos los segmentos'));
        }
        catch(\Exception $e ){
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
        }
    }

    /**
     * @Route("/{id}/delete", name="segmento_delete", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     * @Security("has_role('ROLE_SEGMENTO_DELETE')")
     */
    public function deleteAction(Request $request,Segmento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if ($entity){
            if(!count($entity->getEventos()) && !count($entity->getPlanillas())){
                try {
                    $em->remove($entity);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se eliminó el Segmento'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar eliminar el segmento!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El segmento no debe tener evenos ni planillas de buena fe asociados'));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El segmento no exite'));
    }

    private function canEdit($segmento)
    {
        if ($this->isGranted('ROLE_ADMIN')){
            return true;
        }elseif ($segmento->esCoordinador($this->getUser())){
            return true;
        }

        return false;
    }
}
