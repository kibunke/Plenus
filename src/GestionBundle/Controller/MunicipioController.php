<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CommonBundle\Entity\Municipio;
use CommonBundle\Form\MunicipioType;

/**
 * Municipio controller.
 *
 * @Route("/gestion/municipio")
 * @Security("has_role('ROLE_ADMIN')")
 */
class MunicipioController extends Controller
{
    /**
     * Lists all Municipio entities.
     *
     * @Route("/", name="municipio_list")
     * @Method("GET")
     * @Template("GestionBundle:Municipio:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Route("/list/datatable", name="municipio_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('CommonBundle:Municipio')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $municipio){
            $data['data'][] = array(
                "municipio"  => array(
                                    "id" => $municipio->getId(),
                                    "nombre" => $municipio->getNombre(),
                                    "seccionElectoral" => $municipio->getSeccionElectoral(),
                                    "regionDeportiva" => $municipio->getRegionDeportiva(),
                                    "cruceRegional" => $municipio->getCruceRegional(),
                            ),
                "actions"   => $this->renderView('GestionBundle:Municipio:actions.html.twig', array('entity' => $municipio)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Municipio entity.
     *
     * @Route("/new", name="municipio_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Municipio:new.html.twig")
     */
    public function newAction(Request $request)
    {
        //$em = $this->getDoctrine()->getManager();
        //$entity = new Municipio();
        //$form = $this->createForm(MunicipioType::class, $entity);
        //$form->handleRequest($request);
        //if ($form->isSubmitted() && $form->isValid()) {
        //    $entity->setCreatedBy($this->getUser());
        //    try{
        //        $em->persist($entity);
        //        $em->flush();
        //        return new JsonResponse(array('success' => true, 'message' => 'Se agregó el Municipio.'));
        //    }catch (\Exception $e) {
        //        $error = $e->getMessage();
        //        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
        //    }
        //}
        //return array(
        //    'entity' => $entity,
        //    'form'   => $form->createView(),
        //);
    } 
    
    /**
     * Finds and displays a Municipio entity.
     *
     * @Route("/{id}", name="municipio_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Municipio $id)
    {
    }

    /**
     * Displays a form to edit an existing Municipio entity.
     *
     * @Route("/{id}/edit", name="municipio_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Municipio:edit.html.twig")
     */
    public function editAction(Request $request, Municipio $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(MunicipioType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El Municipio fue modificado.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * @Route("/{id}/delete", name="municipio_delete", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function deleteAction(Request $request,Municipio $entity)
    {
        //$em = $this->getDoctrine()->getManager();
        //if ($entity){
        //    try {
        //        $em->remove($entity);
        //        $em->flush();
        //        return new JsonResponse(array('success' => true, 'message' => 'Se eliminó el Municipio'));
        //    }
        //    catch(\Exception $e ){
        //        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
        //    }
        //}
        //return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El municipio no exite'));
    }    
}