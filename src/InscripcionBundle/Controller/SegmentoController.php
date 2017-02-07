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
     * @Route("/list", name="segmento_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        return array();        
    }
    
    /**
     * show a Segmento entiti.
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
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('InscripcionBundle:Segmento')->datatable($request->request);

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $segmento){
            $data['data'][] = array(
                "id"        => $segmento->getId(),
                "segmento"  => $segmento->getNombreCompletoRaw(),
                "eventos"   => count($segmento->getEventos()),
                "inscriptos"=> 0,//$user->getUsername(),
                "actions"   => $this->renderView('InscripcionBundle:Segmento:actions.html.twig', array('entity' => $segmento)),
            );
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/new", name="segmento_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("InscripcionBundle:Segmento:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $segmento = new Segmento();
        $form = $this->createForm(SegmentoType::class, $segmento);
        //$form = $this->createNewAccountForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            try {
                $segmento->setCreatedBy($this->getUser());
                //foreach($segmento->getEventos() as $evento){
                //    $segmento->addEvento($evento);
                //}
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
    public function deleteAction(Request $request,Segmento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if ($entity){
            if(!count($entity->getEventos())){
                try {
                    $em->remove($entity);
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