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
use InscripcionBundle\Form\PlanillaType;

/**
 * Planilla controller.
 *
 * @Route("/planilla")
 * @Security("has_role('ROLE_INSCRIPCION') and has_role('ROLE_SEGMENTO')")
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
                "inscriptos"   => 0,
                "estado"  => 0,
                "actions"   => $this->renderView('InscripcionBundle:Planilla:actions.html.twig', array('entity' => $planilla)),
            );
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/new", name="planilla_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("InscripcionBundle:Planilla:new.html.twig")
     */
    public function newAction(Request $request)
    {        
        $em = $this->getDoctrine()->getManager();
        $planilla = new Planilla();
        
        $segmento = $em->getRepository('InscripcionBundle:Segmento')->find(13);
        $planilla->setSegmento($segmento);
        
        
        $form = $this->createForm(PlanillaType::class, $planilla);
        //$form = $this->createNewAccountForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            try {
                $planilla->setCreatedBy($this->getUser());
                //foreach($segmento->getEventos() as $evento){
                //    $segmento->addEvento($evento);
                //}
                //$em->persist($segmento);
                //$em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se creo la Planilla'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }
        return array(
                'form' => $form->createView(),
                'planilla' => $planilla
            );
    }
    
    /**
     * @Route("/{id}/edit", name="segmento_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("InscripcionBundle:Segmento:edit.html.twig")
     */
    public function editAction(Request $request,Segmento $segmento)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SegmentoType::class, $segmento);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            try {
                $segmento->setUpdatedAt(new \DateTime());
                $segmento->setUpdatedBy($this->getUser());
                //foreach($segmento->getEventos() as $evento){
                //    $segmento->addEvento($evento);
                //}
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
}